<?php

namespace Pierce;
use Pierce\Connection\Connection,
    Noair\Listener,
    Noair\Event;

class Client extends Listener
{
    private $connections = [];
    private $interrupt   = false;
    private $pollrate    = 10; // in Hz

    private $nick;
    private $username;
    private $realname;

    public function __construct($set = [])
    {
        foreach ($set as $prop => $val):
            if ($name == 'nick' || $name == 'username'):
                $this->$name = str_replace(' ', '', $val);
            else:
                $this->$prop = $val;
            endif;
        endforeach;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $val)
    {
        if (in_array($name, ['nick', 'username', 'realname'])
            && $this->$name
            && $this->$name != $val
        ):
            // send change to servers
        endif;

        if ($name == 'nick' || $name == 'username'):
            $this->$name = str_replace(' ', '', $val);
        elseif ($name != 'connections'):
            $this->$name = $val;
        endif;
    }

    public function addConnection(Connection $conn)
    {
        // install default values if they're not already set
        foreach (['nick', 'username', 'realname'] as $prop):
            if ($conn->$prop == '' && isset($this->$prop)):
                $conn->$prop = $this->$prop;
            endif;
        endforeach;

        $this->connections[$conn->name] = $conn;
    }

    public function listen()
    {
        while (!$this->interrupt && count($this->connections)):
            $this->listenOnce();
            usleep((int) ((1 / $this->pollrate) * 1000000));
        endwhile;
    }

    public function listenOnce($name = null)
    {
        foreach ($this->connections as $conn):
            if (!$this->interrupt && (!isset($name) || $conn->name == $name)):
                $conn->listenOnce();
            endif;
        endforeach;
    }

    public function onDisconnected(Event $e)
    {
        unset($this->connections[$e->data]);
    }

    public function onInterrupt()
    {
        $this->interrupt = true;
    }
}
