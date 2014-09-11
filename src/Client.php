<?php

namespace Pierce;
use DavidRockin\Podiya\Podiya,
    DavidRockin\Podiya\Listener,
    DavidRockin\Podiya\Event,
    Monolog\Logger,
    Pierce\Connection\Connection;

class Client extends Listener
{
    private $logger;
    private $connections = [];
    
    private $nick;
    private $username;
    private $realname;
    
    public function __construct(Podiya $podiya, Logger $logger, $set = [])
    {
        $this->logger = $logger;
        $this->events = [
            ['disconnected', [$this, 'disconnectedHandler']],
        ];
        
        foreach ($set as $prop => $val) {
            $this->$prop = $val;
        }
        
        parent::__construct($podiya);
    }
    
    public function addConnection(Connection $conn)
    {
        // install default values if they're not already set
        foreach (['nick', 'username', 'realname'] as $prop) {
            if ($conn->$prop == '' && isset($this->$prop)) {
                $conn->$prop = $this->$prop;
            }
        }
        
        try {
            $this->connections[$conn->name] = $conn->connect();
        } catch (Exception $e) {
            $this->connections[$conn->name] = null;
            // log something
        }
    }
    
    public function disconnectedHandler(Event $e)
    {
        unset($this->connections[$e->getData()]);
    }
    
    public function listen()
    {
        while (count($this->connections)) {
            $this->listenOnce();
        }
    }
    
    public function listenOnce()
    {
        
    }
}
