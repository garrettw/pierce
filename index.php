<?php
// This file contains the basic code necessary to use PIeRCe.

require 'vendor/autoload.php';
use DavidRockin\Podiya\Podiya,
    DavidRockin\Podiya\Listener,
    DavidRockin\Podiya\Event;

class MyBot extends Listener
{
    public function __construct(Podiya $podiya)
    {
        $this->events = [
            ['sample_event', [$this, 'sampleEventHandler']],
            ['connected', [$this, 'connectedHandler']],
        ];
        parent::__construct($podiya);
    }
    
    public function sampleEventHandler(Event $e)
    {
        // do something, like send a message
        $this->podiya->publish(new Event('channel message', [
            'connection' => 'freenode',
            'channel' => '#pierce-test',
            'message' => 'hi there',
        ], $this));
    }
    
    public function connectedHandler(Event $e)
    {
        $this->events[] = $this->podiya->subscribe('timer:30000', [$this, 'sampleTimedEvent']);
    }
    
    public function sampleTimedEvent(Event $e)
    {
        $this->podiya->publish(new Event('channel message', [
            'connection' => 'freenode',
            'channel' => '#pierce-test',
            'message' => 'hi there',
        ], $this));
    }
}

$dic = new \Dice\Dice();
$dic->addRule('DavidRockin\\Podiya\\Podiya', new \Dice\Rule(['shared' => true]));
$dic->addRule('Pierce\\Client', new \Dice\Rule(['shared' => true]));
$dic->addRule('Monolog\\Logger', new \Dice\Rule(['shared' => true]));

// $client and $bots all connect automatically through the same Podiya instance
// because they're both inside the same Dice.
$client = $dic->create('Pierce\\Client', [[
    'logLevel'   => 0,
]]);

// Pierce\StdEvents is the primary bot needed to translate raw data into IRC
// events and vice-versa. Any custom bots will likely want to subscribe to these
// events rather than the raw data events coming from the connections.
$bots = [
    $dic->create('Pierce\\StdEvents'),
    $dic->create('MyBot'),
];
$client->addConnection($dic->create('Pierce\\Connection\\Connection', [[
    'name' => 'freenode',
    'servers' => [
        'irc.freenode.net' => '6667',
    ],
    // 'bindto' => '0.0.0.0:0',
    'nick' => 'PIeRCe',
    'username' => 'pierce',
    'realname' => 'PIeRCe IRC bot',
    // 'password' => 'none',
    // 'usermode' => 0,
]]));
$client->listen();
$client->destroy();
