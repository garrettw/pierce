<?php
// This file contains the basic code necessary to use PIeRCe.
error_reporting(-1);
/* BEGIN boilerplate code required for every PIeRCe usage. */
require 'vendor/autoload.php';
$dice = new \Dice\Dice();
$dice->addRule('Noair\Noair', ['shared' => true]);
$dice->addRule('Noair\Listener',
    ['call' => [['subscribe', [['instance' => 'Noair\Noair']]]] ]
);
$dice->addRule('Monolog\Logger', ['constructParams' => ['pierce']]);
/* END boilerplate */

/* BEGIN custom bot code */
use Pierce\Event\SendEvent,
    Pierce\Connection\Message,
    Noair\Event;
class MyBot extends Noair\Listener
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
        $this->noair->publish(new SendEvent('privmsg', [
            'connection' => 'freenode',
            'target' => '#pierce-test',
            'message' => 'hi there',
            'priority' => Message::NORMAL,
        ], $this));
    }

    public function onConnected(Event $e)
    {
        $this->handlers[] = $this->noair->subscribe('timer:30000', [$this, 'sampleTimedEvent']);
    }

    public function sampleTimedEvent(Event $e)
    {
        $this->noair->publish(new SendEvent('privmsg', [
            'connection' => 'freenode',
            'target' => '#pierce-test',
            'message' => 'hi there (timed)',
            'priority' => Message::NORMAL,
        ], $this));
    }
}
/* END custom bot code */

/* BEGIN customizable execution code */

// Pierce\Logger encapsulates Monolog\Logger and logs all events to it.
// Pierce\StdEvents is the primary bot needed to translate raw data into IRC
// events and vice-versa. Any custom bots will likely want to subscribe to these
// events rather than the raw data events coming from the connections.

$client = $dice->create('Pierce\Client', [[
        'nick'     => 'PIeRCe',
        'realname' => 'PIeRCe bot',
        'username' => 'pierce',
]]);

$client->addBot($dice->create('Pierce\Logger'))
    ->addBot($dice->create('Pierce\StdEvents', [$client->rxtimeout]))
    ->addBot($dice->create('MyBot'))
    ->addConnection($dice->create('Pierce\Connection', [[
        'name'        => 'freenode',
        'servers'     => ['chat.freenode.net:6667'],
        'type'        => $dice->create('Pierce\Numerics\Factory', ['Ircu']),
        'channels'    => ['#pierce-test'],
        // 'bindto'      => '0.0.0.0:0',
        // 'password'    => '',
        // 'usermode'    => 0,
    ]]), Pierce\Client::CONNECTNOW)
    ->listen()
    ->unsubscribe();

// and that's it!
