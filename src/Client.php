<?php

namespace Pierce;
use Noair\Event;

class Client extends Noair\Listener
{
    private $dice;
    private $connections = [];
    private $bots = [];
    private $interrupt   = false;
    private $pollrate    = 10; // in Hz

    private $nick;
    private $username;
    private $realname;

    public function __construct(Dice\Dice $dice, $set = [])
    {
        $this->dice = $dice;

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
            if ($name != 'realname'):
                $this->$name = str_replace(' ', '', $val);
            endif;

            // send change to servers

        elseif ($name != 'connections'):
            $this->$name = $val;
        endif;
    }

    public function addConnection(array $conndata, $connectnow = false)
    {
        $conn = $this->dice->create('Pierce\\Connection', [$conndata]);

        // install default values if they're not already set
        foreach (['nick', 'username', 'realname'] as $prop):
            if ($conn->$prop == '' && isset($this->$prop)):
                $conn->$prop = $this->$prop;
            endif;
        endforeach;

        $this->connections[$conn->name] = $conn;

        if ($connectnow):
            $this->noair->publish(new Event('connect', $conn->name, $this));
        endif;

        return $this;
    }

    public function addBots($bots)
    {
        if (!is_array($bots)):
            $bots = [$bots];
        endif;

        foreach ($bots as $bot):
            $this->bots[] = $this->dice->create($bot);
        endforeach;

        return $this;
    }

    public function connectAll()
    {
        foreach ($this->connections as $conn):
            if (!$conn->connected):
                $this->noair->publish(new Event('connect', $conn->name, $this));
            endif;
        endforeach;

        return $this;
    }

    public function listen()
    {
        while (!$this->interrupt && count($this->connections)):
            $this->listenOnce();
            usleep((int) ((1 / $this->pollrate) * 1000000));
        endwhile;

        return $this;
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
        unset($this->connections[$e->caller->name]);
    }

    public function onInterrupt()
    {
        $this->interrupt = true;
    }
}
