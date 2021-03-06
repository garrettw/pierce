<?php

namespace Pierce;
use Pierce\Connection\Channel,
    Pierce\Connection\Message,
    Pierce\Connection\User;

class Connection extends \Noair\Listener
{
    private $name;
    private $type;
    private $servers = [];
    private $bindto = '';
    private $nick = '';
    private $username = '';
    private $realname = '';
    private $password = '';
    private $perform = [];
    private $motd = [];
    private $usermode = 0;
    private $channels = [];
    private $users = [];
    private $autoretry = false;
    private $autoretrymax = 3;

    private $sock;
    private $remoteaddr = '';
    private $connected = false;
    private $loggedin  = false;
    private $lastrx    = 0; // in seconds
    private $lasttx    = 0; // in milliseconds
    private $lasttxmsg = 0; // in milliseconds
    private $messagequeue;
    private $sendrate  = 4; // in Hz -- should be <= Client::$pollrate
    private $ef;

    public function __construct(Event\Factory $efactory, array $set = [])
    {
        $this->ef = $efactory;

        foreach ($set as $prop => $val):
            if ($prop == 'nick' || $prop == 'username'):
                $this->$prop = str_replace(' ', '', $val);
            else:
                $this->$prop = $val;
            endif;
        endforeach;

        if (!$this->autoretry):
            $this->autoretrymax = 1;
        endif;

        $this->type = $this->type->create();

        $this->messagequeue = [
            Message::HIGH => $this->perform, Message::NORMAL => [], Message::LOW => [],
        ];
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $val)
    {
        switch ($name):
            case 'nick':
            case 'username':
                $val = str_replace(' ', '', $val);
            case 'realname':
                if ($this->$name != $val):
                    if ($this->connected):
                        $this->noair->publish(
                            $this->ef->create('connectionPropertyChange', [$name, $val], $this)
                        );
                    endif;
                    $this->$name = $val;
                endif;
            case 'name':
            case 'connected':
            case 'loggedin':
            case 'lastrx':
            case 'lasttx':
                break;

            default:
                $this->$name = $val;
        endswitch;
    }

    public function addChannel(Channel $chan)
    {
        $this->channels[$chan->name] = $chan;
        return $this;
    }

    public function channel($name)
    {
        return $this->channels[$name];
    }

    public function removeChannel($name)
    {
        unset($this->channels[$name]);
        return $this;
    }

    public function addUser(User $user)
    {
        $this->users[$user->nick] = $user;
        return $this;
    }

    public function user($nick)
    {
        return $this->users[$nick];
    }

    public function removeUser($nick)
    {
        unset($this->users[$nick]);
        return $this;
    }

    public function listenOnce()
    {
        if (!$this->updateState()):
            return false;
        endif;

        /* send queued messages */
        $nextInterval = $this->lasttx + (int) ((1.0 / $this->sendrate) * 1000);
        if (self::currentTimeMillis() >= $nextInterval):
            foreach ($this->messagequeue as $prio => $msgs):
                if (count($msgs)):
                    $this->sendNow(array_shift($this->messagequeue[$prio]));
                    break;
                endif;
            endforeach;
        endif;

        // check the socket to see if data is waiting for us
        // this will trigger a warning when a signal is received
        $r = [$this->sock];
        $w = null;
        $e = null;
        $result = stream_select($r, $w, $e, 0);
        $rawmsg = null;

        if ($result):
            // the socket has data to read, so read and block until we get an EOL
            $rawmsg = '';
            do {
                if ($get = fgets($this->sock)):
                    $rawmsg .= $get;
                endif;
                $rawlen = strlen($rawmsg);
            } while ($rawlen && $rawmsg{$rawlen - 1} != "\n");

            $rawmsg = trim($rawmsg);
        elseif ($result === false):
            // panic! something went wrong! maybe received a signal.
            // not sure what to do here yet.
            throw new Exception($this->noair, 'stream_select error');
        endif;
        // no data on the socket

        $this->noair->publish($this->ef->create('timer', $rawmsg, $this));

        if (!empty($rawmsg)):
            // received data, so hand each msg off to StdEvents
            $this->lastrx = time();
            $this->noair->publish(
                $this->ef->create('received', new Message($rawmsg), $this)
            );
        endif;

        return true;
    }

    public function motd($text)
    {
        $this->motd[] = $text;
    }

    public function onClientPropertyChange(\Noair\Event $e)
    {
        $prop = $e->data[0];
        if ($this->$prop == $e->caller->$prop):
            $this->__set($prop, $e->data[1]);
        endif;
    }

    public function onConnect(\Noair\Event $e)
    {
        if ($e->data != $this->name):
            return;
        endif;

        if ($this->connected || !$this->subscribed):
            // either we're connected already, or not subscribed so can't do anything
            return $this;
        endif;

        for ($i = 0; $i < $this->autoretrymax; $i++):
            $timeout = ini_get("default_socket_timeout");
            $context = stream_context_create(['socket' => ['bindto' => $this->bindto]]);

            foreach ($this->servers as $address):
                if ($this->sock =
                    stream_socket_client($address, $errno, $errstr, $timeout,
                        STREAM_CLIENT_CONNECT, $context)
                ):
                    if (!stream_set_blocking($this->sock, 0)):
                        throw new Exception($this->noair, 'Unable to unblock stream');
                    endif;

                    $this->remoteaddr = $address;
                    $this->lastrx = time();
                    is_numeric($this->usermode) or $this->usermode = 0;

                    $this->password and $this->sendNow("PASS {$this->password}");
                    $this->sendNow("USER {$this->username} {$this->usermode} * :{$this->realname}");
                    $this->sendNow("NICK {$this->nick}");

                    $this->updateState();
                    return $this;
                endif;

                $faildata = [$this->name, $errno, $errstr];
                $this->noair->publish($this->ef->create('connectFailed', $faildata, $this));
            endforeach;
        endfor;

        $this->onDisconnect($e);
        throw new Exception($this->noair,
            "Unable to connect to any server for connection '{$this->name}'");
    }

    public function onConnectionError(\Noair\Event $e)
    {
        if ($e->data == $this->name):
            return $this->noair->publish(
                $this->ef->create('reconnect', $this->name, $this)
            );
        endif;
    }

    public function onDisconnect(\Noair\Event $e)
    {
        if ($e->data != $this->name):
            return;
        endif;

        fclose($this->sock);
        $this->noair->publish($this->ef->create('disconnected', $this->name, $this));
        $this->updateState();
        return $this->unsubscribe();
    }

    public function onNewNickFromServer(\Noair\Event $e)
    {
        if ($e->caller->name == $this->name):
            $this->nick = $e->data;
            // more may be needed here, like to update our User object and such
        endif;
    }

    public function onReconnect(\Noair\Event $e)
    {
        if ($e->data == $this->name):
            $this->connected = false;
            // reset stream/socket stuff
            return $this->onConnect($e);
        endif;
    }

    public function onRawSend(Event\RawSendEvent $rse)
    {
        if ($rse->data['connection'] == $this->name):
            if ($rse->data['priority'] == Message::URGENT):
                return $this->sendNow($rse->data['message']);
            else:
                $this->messagequeue[$rse->data['priority']][] = $rse->data['message'];
            endif;
        endif;
    }

    public function onRxTimeout(\Noair\Event $e)
    {
        if ($e->data == $this->name):
            return $this->noair->publish(
                $this->ef->create('connectionError', $this->name, $this)
            );
        endif;
    }

    private function sendNow($message)
    {
        if (($result = fwrite($this->sock, $message . "\r\n")) !== false):
            $this->noair->publish($this->ef->create('sent', $message, $this));
            $this->lasttx = self::currentTimeMillis();
            return true;
        endif;

        $this->noair->publish($this->ef->create('connectionError', $this->name, $this));
        return false;
    }

    private function updateState()
    {
        if ($this->sock !== false && is_resource($this->sock)
            && strtolower(get_resource_type($this->sock)) == 'stream'
        ):
            return ($this->connected = true);
        endif;

        return ($this->connected = false);
    }

    /**
     * Returns the current timestamp in milliseconds.
     * Named for the similar function in Java.
     *
     * @internal
     * @return  int Current timestamp in milliseconds
     * @since   1.0
     * @version 1.0
     */
    final public static function currentTimeMillis()
    {
        // microtime(true) returns a float where there's 4 digits after the
        // decimal and if you add 00 on the end, those 6 digits are microseconds.
        // But we want milliseconds, so bump that decimal point over 3 places.
        return (int) (microtime(true) * 1000);
    }
}
