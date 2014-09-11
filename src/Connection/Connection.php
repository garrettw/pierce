<?php

namespace Pierce\Connection;
use DavidRockin\Podiya\Podiya,
    DavidRockin\Podiya\Listener,
    DavidRockin\Podiya\Event,
    Monolog\Logger;

class Connection extends Listener
{
    private $logger;
    
    private $name;
    private $servers = [];
    private $bindto;
    private $nick = '';
    private $username = '';
    private $realname = '';
    
    private $connected = false;
    
    public function __construct(Podiya $podiya, Logger $logger, $set = [])
    {
        $this->logger = $logger;
        $this->events = [
            ['send', [$this, 'sendHandler']],
        ];
        
        foreach ($set as $prop => $val) {
            $this->$prop = $val;
        }
        
        parent::__construct($podiya);
    }
    
    public function __get($name)
    {
        return $this->$name;
    }
    
    public function __set($name, $val)
    {
        if (in_array($name, ['nick', 'username', 'realname'])) {
            if ($this->connected) {
                // send change to server
            }
            $this->$name = $val;
        }
    }
    
    public function connect()
    {
        $this->podiya->publish(new Event('connected'));
        $this->connected = true;
        return $this;
    }
    
    public function disconnect()
    {
        $this->podiya->publish(new Event('disconnected'));
        $this->connected = false;
    }
    
    public function reconnect()
    {
        
    }
    
    public function sendHandler(Event $e)
    {
        
    }
    
    public function listenOnce()
    {
        
    }
}
