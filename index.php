<?php
// This file contains the basic code necessary to use PIeRCe.

require 'vendor/autoload.php';
use Noair\Noair,
    Noair\Listener,
    Noair\Event;

class MyBot extends Listener
{
    public function __construct()
    {
        $this->handlers = [
            ['sampleEvent', [$this, 'sampleEventHandler']],
        ];
    }

    public function sampleEventHandler(Event $e)
    {
        // do something, like send a message
        $this->noair->publish(new Event('privmsg', [
            'connection' => 'freenode',
            'channel' => '#pierce-test',
            'message' => 'hi there',
        ], $this));
    }

    public function onConnected(Event $e)
    {
        $this->handlers[] = $this->noair->subscribe('timer:30000', [$this, 'sampleTimedEvent']);
    }

    public function sampleTimedEvent(Event $e)
    {
        $this->noair->publish(new Event('privmsg', [
            'connection' => 'freenode',
            'channel' => '#pierce-test',
            'message' => 'hi there (timed)',
        ], $this));
    }
}

$dic = new \Dice\Dice();
$dic->addRule('Noair\\Noair', new \Dice\Rule(['shared' => true]));
$dic->addRule('Pierce\\Client', new \Dice\Rule(['shared' => true]));

// $client and $bots all connect automatically through the same Noair instance
// because the Noair instance is marked to be shared within the same Dice.
// Pierce\Logger encapsulates Monolog\Logger and logs all events to it.
// Pierce\StdEvents is the primary bot needed to translate raw data into IRC
// events and vice-versa. Any custom bots will likely want to subscribe to these
// events rather than the raw data events coming from the connections.
$noair = $dic->create('Noair\\Noair');
$client = $dic->create('Pierce\\Client', [[
    'username' => 'pierce',
    'realname' => 'PIeRCe IRC bot',
]])->subscribe($noair);

$bots = [
    $dic->create('Pierce\\Logger')->subscribe($noair),
    $dic->create('Pierce\\StdEvents')->subscribe($noair),
    $dic->create('MyBot')->subscribe($noair),
];

$client->addConnection($dic->create('Pierce\\Connection\\Connection', [[
    'name' => 'freenode',
    'servers' => [
        'irc.freenode.net' => '6667',
    ],
    // 'bindto' => '0.0.0.0:0',
    'nick' => 'PIeRCe',
    // 'password' => 'none',
    // 'usermode' => 0,
    'channels' => ['#pierce-test'],
]])->subscribe($noair)->connect());

$client->listen();
$client->unsubscribe();
