<?php

namespace Pierce;

class Client extends \Noair\Listener
{
    const CONNECTNOW = true;
    const CONNECTLATER = false;

    private $connections = [];
    private $bots        = [];
    private $interrupt   = false;
    private $pollrate    = 10; // in Hz
    private $rxtimeout   = 300;
    private $ef;

    private $nick = '';
    private $username = '';
    private $realname = '';
    private $version = '';

    public function __construct(Event\Factory $efactory, $set = [])
    {
        $this->ef = $efactory;

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
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $val)
    {
        switch ($name):
            case 'nick':
                // intentional fallthrough
            case 'username':
                $val = str_replace(' ', '', $val);
                // intentional fallthrough
            case 'realname':
                if ($this->$name && $this->$name != $val):
                    $this->noair->publish(
                        $this->ef->create('clientPropertyChange', [$name, $val], $this)
                    );
                    $this->$name = $val;
                endif;
                // intentional fallthrough
            case 'connections':
                break;

            default:
                $this->$name = $val;
        endswitch;
    }

    public function addConnection(Connection $conn, $connectnow = self::CONNECTLATER)
    {
        // install default values if they're not already set
        foreach (['nick', 'username', 'realname'] as $prop):
            if ($conn->$prop == '' && isset($this->$prop)):
                $conn->$prop = $this->$prop;
            endif;
        endforeach;

        $this->connections[$conn->name] = $conn;

        if ($connectnow == self::CONNECTNOW):
            $this->noair->publish($this->ef->create('connect', $conn->name, $this));
        endif;

        return $this;
    }

    public function addBot($bot)
    {
        if (is_array($bot)):
            foreach ($bot as $each):
                $this->addBot($each);
            endforeach;
        else:
            $this->bots[] = $bot;
        endif;
        return $this;
    }

    public function connectAll()
    {
        foreach ($this->connections as $conn):
            if (!$conn->connected):
                $this->noair->publish($this->ef->create('connect', $conn->name, $this));
            endif;
        endforeach;

        return $this;
    }

    public function listen()
    {
        while ($this->listenOnce() && count($this->connections)):
            usleep((int) ((1 / $this->pollrate) * 1000000));
        endwhile;

        return $this;
    }

    public function listenOnce($name = null)
    {
        foreach ($this->connections as $conn):
            if ($this->interrupt):
                return false;
            elseif (!isset($name) || $conn->name == $name):
                $conn->listenOnce();
            endif;
        endforeach;
        return true;
    }

    public function onDisconnected(\Noair\Event $e)
    {
        unset($this->connections[$e->caller->name]);
    }

    public function onInterrupt()
    {
        $this->interrupt = true;
    }
}
