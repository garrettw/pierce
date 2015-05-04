<?php
namespace Pierce;
use Pierce\Connection\Channel,
    Pierce\Connection\Message,
    Pierce\Connection\User,
    Noair\Event,
    Pierce\Event\SendEvent;

class StdEvents extends \Noair\Listener
{
    private $rxtimeout;
    private $ef;
    private $sef;
    private $rsef;

    public function __construct(\Pierce\Event\Factory $e, \Pierce\Event\Factory $se,
        \Pierce\Event\Factory $rse, $rxt)
    {
        $this->ef = $e;

        $se->type = 'Send';
        $this->sef = $se;

        $rse->type = 'RawSend';
        $this->rsef = $rse;

        $this->defaultPriority = \Noair\Noair::PRIORITY_HIGHEST;
        $this->rxtimeout = $rxt;
        $this->handlers = [
            ['timer:' . ($this->rxtimeout / 8), [$this, 'pingCheck']],
        ];
    }

    public function pingCheck(Event $e)
    {
        if (!empty($e->data)):
            return;
        endif;

        $conn = $e->caller;
        $time = time();

        if ($conn->lastrx + $this->rxtimeout < $time):
            $this->noair->publish($this->ef->create('rxTimeout', $conn->name, $this));
        elseif ($conn->lastrx + $this->rxtimeout/2 < $time):
            $this->noair->publish($this->rsef->create([
                'connection' => $conn->name,
                'message' => 'PING ' . $conn->remoteaddr,
                'priority' => Message::URGENT,
            ], $this));
        endif;

        // cut ':port' out of $conn->remoteaddr above
    }

    public function onConnectionPropertyChange(Event $e)
    {
        switch ($e->data[0]):
            case 'nick':
                $this->noair->publish($this->rsef->create([
                    'connection' => $e->caller->name,
                    'message' => 'NICK ' . $e->data[1],
                    'priority' => Message::URGENT,
                ], $this));
                break;

            case 'username':

            case 'realname':

        endswitch;
    }

    public function onReceived(Event $e)
    {
        $msg = $e->data;
        // analyze incoming message and re-publish a more specific event
        if (is_numeric($msg->cmd) && isset($e->caller->type->code[$msg->cmd])):
            $cmdName = strtolower($e->caller->type->code[$msg->cmd]);
        else:
            $cmdName = strtolower($msg->cmd);
        endif;

        if (strpos($cmdName, '_')) {
            $parts = explode('_', $cmdName);
            $partcount = count($parts);

            for ($i = 1; $i < $partcount; $i++) {
                $parts[$i] = ucfirst($parts[$i]);
            }

            $cmdName = implode($parts);
        }

        $this->noair->publish($this->ef->create($cmdName, $msg, $e->caller));
    }

    public function onChanmsg(Event $e)
    {

    }

    public function onChanmsgAction(Event $e)
    {

    }

    public function onChannotice(Event $e)
    {

    }

    public function onConnected(Event $e)
    {
        foreach ($e->caller->channels as $chan):
            if (is_array($chan)):
                $chan = $chan[0] . ' ' . $chan[1];
            endif;

            $this->noair->publish($this->sef->create('join', [
                'connection' => $e->caller->name,
                'message' => $chan,
                'priority' => Message::HIGH,
            ], $e->caller));
        endforeach;
    }

    public function onCtcp(Event $e)
    {
        // fill in next; gotta respond if enabled & recognized
    }

    public function onCtcpReply(Event $e)
    {

    }

    public function onErrNicknameinuse(Event $e)
    {
        $e->caller->nick = substr($e->caller->nick, 0, 6) . rand(0, 999);
    }

    public function onErrNomotd(Event $e)
    {
        $this->noair->publish(
            $this->ef->create('connected', $e->caller->name, $e->caller)
        );
    }

    public function onErrNoopermotd(Event $e)
    {
        $this->noair->publish(
            $this->ef->create('connected', $e->caller->name, $e->caller)
        );
    }

    public function onJoin(Event $e)
    {
        $channelName = $e->data->params[0];

        // did we just join a channel, or did someone else join one we're in?
        if ($e->caller->nick == $e->data->srcNick):
            // it's us joining, so create channel obj to receive info later
            $e->caller->addChannel(new Channel($channelName));
            // we'll be receiving topic and namreply

            // ask for channel's modes
            $this->noair->publish($this->sef->create('mode', [
                'connection' => $e->caller->name,
                'message' => $channelName,
                'priority' => Message::HIGH,
            ], $e->caller));

            // ask for data on all channel users
            $this->noair->publish($this->sef->create('who', [
                'connection' => $e->caller->name,
                'message' => $channelName,
                'priority' => Message::NORMAL,
            ], $e->caller));

            // ask for channel banlist
            $this->noair->publish($this->sef->create('mode', [
                'connection' => $e->caller->name,
                'message' => "$channelName b",
                'priority' => Message::NORMAL,
            ], $e->caller));
        else:
            // it's someone else, ask for his data
            $this->noair->publish($this->sef->create('who', [
                'connection' => $e->caller->name,
                'message' => $e->data->srcNick,
                'priority' => Message::NORMAL,
            ], $e->caller));

            // retrieve chan, add user.
            $user = new User($e->data->srcNick);
            $e->caller->addUser($user);
            $e->caller->channel($channelName)->addUser($user);
        endif;
    }

    public function onKick(Event $e)
    {
        // is it us? remove user from channel
    }

    public function onMode(Event $e)
    {
        // is it us? update user
    }

    public function onNick(Event $e)
    {
        // is it us? find user and update him
    }

    public function onNotice(Event $e)
    {

    }

    public function onQuit(Event $e)
    {
        // remove user from channel
    }

    public function onPart(Event $e)
    {
        // is it us? remove user from channel
    }

    public function onPing(Event $e)
    {
        $this->noair->publish($this->rsef->create([
            'connection' => $e->caller->name,
            'message' => 'PONG :' . $e->data->body,
            'priority' => Message::URGENT,
        ], $this));
    }

    public function onPrivmsg(Event $e)
    {

    }

    public function onPrivmsgAction(Event $e)
    {

    }

    public function onRplWelcome(Event $e)
    {
        $this->noair->publish(
            $this->ef->create('newNickFromServer', $e->data->params[0], $e->caller)
        );
    }

    public function onRplBounce(Event $e)
    {
        // fill in later; parse server abilities?
    }

    public function onRplMotdstart(Event $e)
    {
        $e->caller->motd($e->data->body);
    }

    public function onRplMotd(Event $e)
    {
        $e->caller->motd($e->data->body);
    }

    public function onRplEndofmotd(Event $e)
    {
        $e->caller->motd($e->data->body);
        $this->noair->publish(
            $this->ef->create('connected', $e->caller->name, $e->caller)
        );
    }

    public function onRplOmotdend(Event $e)
    {
        $this->noair->publish(
            $this->ef->create('connected', $e->caller->name, $e->caller)
        );
    }

    public function onRplEndofo(Event $e)
    {
        $this->noair->publish(
            $this->ef->create('connected', $e->caller->name, $e->caller)
        );
    }

    public function onRplUmodeis(Event $e)
    {
        $e->caller->usermode = $e->data->body;
    }

    public function onRplChannelmodeis(Event $e)
    {
        // fill in next
    }

    public function onRplWhoreply(Event $e)
    {
        $user = $e->caller->user($e->data->params[5]);
        $user->username = $e->data->params[2];
        $user->host = $e->data->params[3];
        $user->server = $e->data->params[4];

        $flags = $e->data->params[6];
        $user->away = ($flags{0} == 'G');

        $umode = 'n';
        $flagslen = strlen($flags);
        if ($flagslen > 1):
            $user->ircop = ($flags{1} == '*');

            if (($user->ircop && $flagslen > 2) || !$user->ircop):
                $umode = $flags{$flagslen - 1};
            endif;
        endif;

        $modes = ['~' => 'founder', '&' => 'admin', '@' => 'op',
            '%' => 'hop', '+' => 'voice', 'n' => 'none',
        ];
        $user->channels[$e->data->params[1]] = $modes[$umode];

        $splitpoint = strpos($e->data->body, ' ');
        $user->hopcount = (int) substr($e->data->body, 0, $splitpoint);
        $user->realname = substr($e->data->body, $splitpoint + 1);
    }

    public function onRplNamreply(Event $e)
    {
        $chan = $e->data->params[2];

        $users = explode(' ', rtrim($e->data->body));
        foreach ($users as $user):
            $modes = ['~' => 'founder', '&' => 'admin', '@' => 'op',
                '%' => 'hop', '+' => 'voice',
            ];

            if (isset($modes[$user{0}])):
                $e->caller->channel($chan)
                    ->addUser(new User(substr($user, 1)), $modes[$user{0}]);
            else:
                $e->caller->channel($chan)->addUser(new User($user));
            endif;
        endforeach;
    }

    public function onRplBanlist(Event $e)
    {
        // fill in next
    }

    public function onRplTopic(Event $e)
    {
        $e->caller->channel($e->data->params[0])->topic = $e->data->body;
    }

    public function onSendJoin(SendEvent $e)
    {
        $this->noair->publish($this->rsef->create([
            'connection' => $e->data['connection'],
            'message' => 'JOIN ' . $e->data['message'],
            'priority' => $e->data['priority'],
        ], $this));
    }

    public function onSendMode(SendEvent $e)
    {
        $this->noair->publish($this->rsef->create([
            'connection' => $e->data['connection'],
            'message' => 'MODE ' . $e->data['message'],
            'priority' => $e->data['priority'],
        ], $this));
    }

    public function onSendWho(SendEvent $e)
    {
        $this->noair->publish($this->rsef->create([
            'connection' => $e->data['connection'],
            'message' => 'WHO ' . $e->data['message'],
            'priority' => $e->data['priority'],
        ], $this));
    }

    public function onSendNotice(SendEvent $e)
    {
        $this->noair->publish($this->rsef->create([
            'connection' => $e->data['connection'],
            'message' => 'NOTICE ' . $e->data['target'] . ' :' . $e->data['message'],
            'priority' => $e->data['priority'],
        ], $this));
    }

    public function onSendPrivmsg(SendEvent $e)
    {
        $this->noair->publish($this->rsef->create([
            'connection' => $e->data['connection'],
            'message' => 'PRIVMSG ' . $e->data['target'] . ' :' . $e->data['message'],
            'priority' => $e->data['priority'],
        ], $this));
    }

    public function onSendQuit(SendEvent $e)
    {
        $message = isset($e->data['message'])
            ? " :" . $e->data['message']
            : "";

        $this->noair->publish($this->rsef->create([
            'connection' => $e->data['connection'],
            'message' => 'QUIT' . $message,
            'priority' => $e->data['priority'],
        ], $this));

        $this->noair->publish(
            $this->ef->create('disconnect', $e->data['connection'], $this)
        );
    }

    public function onTopic(Event $e)
    {
        // fill in next; update channel topic
    }
}
