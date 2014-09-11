<?php
namespace Pierce;
use DavidRockin\Podiya\Podiya,
    DavidRockin\Podiya\Listener,
    DavidRockin\Podiya\Event,
    Monolog\Logger;

class StdEvents extends Listener
{
    private $client;
    
    public function __construct(Podiya $podiya, Client $client)
    {
        $this->client = $client;
        
        $interval = $this->client->rxtimeout * 125;
        $this->events = [
            ["timer:$interval", [$this, 'pingCheck']],
        ];
        parent::__construct($podiya);
    }
    
    public function pingCheck(Event $e)
    {
        $time = time();
        $caller = $e->getCaller();
        
        if ($time - $caller->_lastrx > $caller->_rxtimeout) {
            $caller->reconnect();
            $caller->_lastrx = $time;
        } elseif ($time - $caller->_lastrx > $caller->_rxtimeout/2) {
            $this->podiya->publish(new Event('send', [
                'connection' => $caller->name,
                'message' => 'PING '.$caller->address,
                'priority' => SMARTIRC_CRITICAL,
            ], $this));
        }
    }
}
