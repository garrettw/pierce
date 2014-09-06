<?php
// This file contains the basic code necessary to use PIeRCe.

require 'vendor/autoload.php';
use DavidRockin\Podiya\Podiya,
    DavidRockin\Podiya\Listener,
    DavidRockin\Podiya\Event;

class MyBot implements Listener
{
    private $eventmgr;
    private $client;
    private $events = [];
    
    public function __construct(Podiya $eventmgr, \Pierce\Client $client)
    {
        $this->eventmgr = $eventmgr;
        $this->client = $client;
        $this->events = [
            ['sample_event', [$this, 'sampleEventHandler']],
            ['connected', [$this, 'connected']],
        ];
        
        $this->eventmgr->subscribe_array($this->events);
    }
    
    public function destroy()
    {
        $this->eventmgr->unsubscribe_array($this->events);
    }
    
    public function sampleEventHandler(Event $e)
    {
        // do something, like send a message
        $this->client->connection('freenode')->doSomething();
    }
    
    public function connected(Event $e)
    {
        $this->events[] = $this->eventmgr->subscribe('timer:30000', [$this, 'sampleTimedEvent']);
    }
    
    public function sampleTimedEvent(Event $e)
    {
        $this->client->connection('freenode')->doSomething();
    }
}

$dic = new \Dice\Dice();
$dic->addRule('DavidRockin\\Podiya\\Podiya', new \Dice\Rule(['shared' => true]));
$dic->addRule('Pierce\\Client', new \Dice\Rule(['shared' => true]));

// $client and $bots connect automatically through the same Podiya instance
// because they're both inside the same Dice.
$client = $dic->create('Pierce\\Client', [
    'useSockets' => true,
    'logLevel'   => 0,
]);
$bots = [
    $dic->create('Pierce\\StdEvents'),
    $dic->create('MyBot'),
];
$client->addConnection($dic->create('Pierce\\Connection\\Connection', [
    'name' => 'freenode',
    'servers' => [
        'irc.freenode.net' => '6667',
    ],
]));
$client->connectAll();
$client->listen();
$client->disconnect();
