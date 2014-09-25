<?php

namespace Pierce\Connection;
use Pierce\Exception as PException,
    Noair\Noair,
    Noair\Listener,
    Noair\Event;

class Connection extends Listener
{
    private $name;
    private $servers = [];
    private $bindto;
    private $nick = '';
    private $username = '';
    private $realname = '';
    private $password = '';
    private $perform = [];
    private $motd;
    private $usermode;
    private $channels = [];
    private $users = [];
    private $autoretry = false;
    private $autoretrycount = 0;
    private $autoretrymax = 3;

    private $sock;
    private $remoteaddr = '';
    private $connected = false;
    private $loggedin  = false;
    private $lastrx    = 0;
    private $lasttx    = 0;
    private $lasttxmsg = 0;

    public function __construct($set = [])
    {
        foreach ($set as $prop => $val):
            if ($name == 'nick' || $name == 'username'):
                $this->$name = str_replace(' ', '', $val);
            else:
                $this->$prop = $val;
            endif;
        endforeach;

        if (!$this->username):
            $this->username = str_replace(' ', '', exec('whoami'));
        endif;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $val)
    {
        if ($this->connected && in_array($name, ['nick', 'username', 'realname'])
            && $this->$name != $val
        ):
            // send change to server

        elseif (in_array($name, ['name', 'connected', 'loggedin', 'lastrx', 'lasttx'])):
            return;
        endif;

        if ($name == 'nick' || $name == 'username'):
            $this->$name = str_replace(' ', '', $val);
        else:
            $this->$name = $val;
        endif;
    }

    public function connect()
    {
        if ($this->connected || !$this->subscribed):
            return $this;
        endif;

        $timeout = ini_get("default_socket_timeout");
        $context = stream_context_create(['socket' => ['bindto' => $this->bindto]]);

        foreach ($this->servers as $host => $port):
            if ($this->sock =
                stream_socket_client("tcp://{$host}:{$port}",
                    $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context)
            ):
                if (!stream_set_blocking($this->sock, 0)):
                    throw new PException($this->noair, 'Unable to unblock stream');
                endif;

                $this->connected = true;
                $this->remoteaddr = "{$host}:{$port}";
                $this->lastrx = $this->lasttx = self::currentTimeMillis();

                if ($this->password):
                    $this->send('PASS ' . $this->password, Message::URGENT);
                endif;

                $this->send('NICK ' . $this->nick, Message::URGENT);

                if (!is_numeric($this->usermode)):
                    $this->usermode = 0;
                endif;

                $this->send("USER {$this->username} {$this->usermode} * :{$this->realname}",
                            Message::URGENT);

                foreach($this->perform as $cmd):
                    $this->send($cmd, Message::HIGH);
                endforeach;

                if ($this->channels):
                    $this->noair->publish(new Event('join', $this->channels, $this));
                endif;

                break;
            else:
                $faildata = [$this->name, $errno, $errstr];
                $this->noair->publish(new Event('connectFailed', $faildata, $this));
            endif;
        endforeach;

        if ($this->connected):
            $this->noair->publish(new Event('connected', $this->name, $this));
        else:
            if ($this->autoretry && $this->autoretrycount < $this->autoretrymax):
                 $this->autoretrycount++;
                 $this->connect();
            else:
                $this->autoretrycount = 0;
                throw new PException($this->noair,
                    "Unable to connect to any server for connection '{$this->name}'");
            endif;
        endif;

        return $this;
    }

    public function listenOnce()
    {
        /* check state */

        if ($this->loggedin):
            $this->noair->publish(new Event('timer', null, $this));
        endif;

        /* send queued messages */

        /* read data from stream/socket */

        /* if read failed, socket is broken, reconnect */

        /* if no data, check for timeout */

        // if received data, hand each msg off to StdEvents
        $this->noair->publish(new Event('received', new Message($msg), $this));

        /* if connection is broken, log and fix */
    }

    private function send($msg, $priority = Message::NORMAL)
    {
        $this->noair->publish(new Event('send', [
            'connection' => $this->name,
            'message' => $msg,
            'priority' => $priority,
        ], $this));
    }

    public function onDisconnect(Event $e = null)
    {
        if ($this->connected && !isset($e) || $e->data == $this->name):
            $this->noair->publish(new Event('disconnected', $this->name, $this));
            $this->connected = false;
            return $this->unsubscribe();
        endif;
    }

    public function onReconnect(Event $e = null)
    {
        if (!isset($e) || $e->data == $this->name):
            $this->connected = false;
            // reset stream/socket stuff
            return $this->connect();
        endif;
    }

    public function onSend(Event $e)
    {
        if ($e->data['connection'] == $this->name):
            //queue message
            if (!empty($e->data['expectResponse'])):
                $wait = $e->caller->rxtimeout;
                $this->noair->subscribe("timer:$wait", [$this, 'rxtimeout']);
            endif;
        endif;
    }

    public function rxtimeout(Event $e)
    {
        if ($e->data['connection'] == $this->name):

        endif;
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
    final protected static function currentTimeMillis()
    {
        // microtime(true) returns a float where there's 4 digits after the
        // decimal and if you add 00 on the end, those 6 digits are microseconds.
        // But we want milliseconds, so bump that decimal point over 3 places.
        return (int) (microtime(true) * 1000);
    }
}
