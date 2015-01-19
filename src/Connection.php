<?php

namespace Pierce;
use Noair\Event,
    Pierce\Event\RawSendEvent,
    Pierce\Connection\Message;

class Connection extends \Noair\Listener
{
    private $name;
    private $type;
    private $servers = [];
    private $bindto;
    private $nick = '';
    private $username = '';
    private $realname = '';
    private $password = '';
    private $perform = [];
    private $motd = [];
    private $usermode;
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
    private $rxtimeout = 300;

    public function __construct(array $set = [], \Dice\Dice $dice)
    {
        foreach ($set as $prop => $val):
            if ($prop == 'nick' || $prop == 'username'):
                $this->$prop = str_replace(' ', '', $val);
            else:
                $this->$prop = $val;
            endif;
        endforeach;

        if (!$this->username):
            $this->username = str_replace(' ', '', exec('whoami'));
        endif;

        if (!$this->autoretry):
            $this->autoretrymax = 1;
        endif;

        if (!isset($this->type)):
            $map = [
                'quakenet' => 'Asuka',
                'austnet' => 'AustHex',
                'dalnet' => 'Bahamut',
                'ircnet' => 'IRCnet',
                'freenode' => 'Ircu',
                'undernet' => 'Ircu',
            ];
            $lowername = strtolower($this->name);

            if (array_key_exists($lowername, $map)):
                $this->type = $map[$lowername];
            else:
                $this->type = 'RFC';
            endif;
        endif;
        $this->type = $dice->create('Pierce\\Numerics\\' . $this->type);

        $this->messagequeue = [
            Message::HIGH => [], Message::NORMAL => [], Message::LOW => [],
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
                if ($this->connected && $this->$name != $val):
                    $this->noair->publish(new Event('connectionPropertyChange',
                        [$name, $val], $this));
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

    public function onClientPropertyChange(Event $e)
    {
        $prop = $e->data[0];
        if ($this->$prop == $e->caller->$prop):
            $this->__set($prop, $e->data[1]);
        endif;
    }

    public function onConnect(Event $e)
    {
        if ($e->data != $this->name):
            return;
        endif;

        if ($this->connected || !$this->subscribed):
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

                    if ($this->password):
                        $this->rawSend('PASS ' . $this->password, Message::URGENT);
                    endif;

                    $this->rawSend('NICK ' . $this->nick, Message::URGENT);

                    if (!is_numeric($this->usermode)):
                        $this->usermode = 0;
                    endif;

                    $this->rawSend("USER {$this->username} {$this->usermode} * :{$this->realname}",
                                Message::URGENT);

                    foreach($this->perform as $cmd):
                        $this->rawSend($cmd, Message::HIGH);
                    endforeach;

                    if ($this->channels):
                        $this->noair->publish(new Event('join', $this->channels, $this));
                    endif;

                    $this->connected = true;
                    $this->noair->publish(new Event('connected', $this->name, $this));
                    return $this;
                endif;

                $faildata = [$this->name, $errno, $errstr];
                $this->noair->publish(new Event('connectFailed', $faildata, $this));
            endforeach;
        endfor;

        $this->onDisconnect();
        throw new Exception($this->noair,
            "Unable to connect to any server for connection '{$this->name}'");
    }

    public function listenOnce()
    {
        if (!$this->updateState()):
            return false;
        endif;

        $this->noair->publish(new Event('timer', null, $this));

        /* send queued messages */
        if (self::currentTimeMillis()
            >= $this->lasttx + (int) ((1.0 / $this->sendrate) * 1000)
        ):
            if (count($this->messagequeue[Message::HIGH])):
                $this->rawSend(array_shift($this->messagequeue[Message::HIGH]), Message::HIGH);
            elseif (count($this->messagequeue[Message::NORMAL])):
                $this->rawSend(array_shift($this->messagequeue[Message::NORMAL]));
            elseif (count($this->messagequeue[Message::LOW])):
                $this->rawSend(array_shift($this->messagequeue[Message::LOW]), Message::LOW);
            endif;
        endif;

        // check the socket to see if data is waiting for us
        // this will trigger a warning when a signal is received
        $result = stream_select($r = array($this->sock), $w = null, $e = null, 0);
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

        elseif ($result === false):
            // panic! something went wrong! maybe received a signal.
            // not sure what to do here yet.
            die;
        endif;
        // no data on the socket

        $rawmsg = trim($rawmsg);
        $time = time();
        /* if no data, check for rx timeout */
        if (empty($rawmsg)):
            if ($this->lastrx + $this->rxtimeout <= $time):
                $this->noair->publish(new Event('rxTimeout', null, $this));
            endif;
            return;
        endif;

        // received data, so hand each msg off to StdEvents
        $this->lastrx = $time;
        $this->noair->publish(new Event('received', new Message($rawmsg), $this));
    }

    private function rawSend($msg, $priority = Message::NORMAL)
    {
        $this->noair->publish(new RawSendEvent([
            'message' => $msg,
            'priority' => $priority,
        ], $this));
    }

    public function onConnectionError(Event $e)
    {
        if ($e->data != $this->name):
            return;
        endif;

        return $this->noair->publish(new Event('reconnect', $this->name, $this));
    }

    public function onDisconnect(Event $e = null)
    {
        if (isset($e) && $e->data != $this->name):
            return;
        endif;

        $this->noair->publish(new Event('disconnected', $this->name, $this));
        $this->connected = false;
        return $this->unsubscribe();
    }

    public function onReconnect(Event $e)
    {
        if ($e->data != $this->name):
            return;
        endif;

        $this->connected = false;
        // reset stream/socket stuff
        return $this->onConnect($e);
    }

    public function onRawSend(RawSendEvent $rse)
    {
        if ($rse->data['connection'] != $this->name):
            return;
        endif;

        if ($rse->data['priority'] == Message::URGENT):
            $this->sendNow($rse->data['message']);
        else:
            $this->messagequeue[$rse->data['priority']][] = $rse->data['message'];
        endif;
    }

    private function sendNow($message)
    {
        if (!$this->updateState()):
            return false;
        endif;

        if (($result = fwrite($this->sock, $message . "\r\n")) !== false):
            $this->noair->publish(new Event('sent', $message, $this));
            $this->lasttx = self::currentTimeMillis();
            return true;
        endif;

        $this->noair->publish(new Event('connectionError', $this->name, $this));
        return false;
    }

    public function onRxTimeout(Event $e)
    {
        return $this->noair->publish(new Event('connectionError', $this->name, $this));
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
