<?php
// This file contains the basic code necessary to use PIeRCe.
error_reporting(-1);
/* BEGIN boilerplate code required for every PIeRCe usage. */
require 'vendor/autoload.php';
$dice = new \Dice\Dice(true);
$dice->addRule('Pierce\\Client', new \Dice\Rule(['shared' => true]));
$dice->addRule('Noair\\Noair', new \Dice\Rule(['shared' => true]));
$dice->addRule('Noair\\Listener',
    new \Dice\Rule([
        'call' => [['subscribe', [$dice->create('Noair\\Noair')]]]
    ])
);
$dice->addRule('Monolog\\Logger',
    new \Dice\Rule(['constructParams' => ['pierce']])
);
/* END boilerplate */

/* BEGIN custom bot code */
use Noair\Event;
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
/* END custom bot code */

/* BEGIN customizable execution code */

// Pierce\Logger encapsulates Monolog\Logger and logs all events to it.
// Pierce\StdEvents is the primary bot needed to translate raw data into IRC
// events and vice-versa. Any custom bots will likely want to subscribe to these
// events rather than the raw data events coming from the connections.

$client = $dice
    ->create('Pierce\\Client', [[
        'realname' => 'PIeRCe bot',
        // 'username' => 'pierce',
    ]])
    ->addBot([$dice->create('Pierce\\Logger'), $dice->create('Pierce\\StdEvents')])
    ->addBot($dice->create('MyBot'))
    ->addConnection($dice->create('Pierce\\Connection', [[
        'name'        => 'freenode',
        'servers'     => ['chat.freenode.net:6667'],
        'nick'        => 'PIeRCe',
        'channels'    => ['#pierce-test'],
        // 'type'        => 'Ircu',
        // 'bindto'      => '0.0.0.0:0',
        // 'password'    => '',
        // 'usermode'    => 0,
    ]]), Pierce\Client::CONNECTNOW)
    ->listen()
    ->unsubscribe();

// and that's it!
