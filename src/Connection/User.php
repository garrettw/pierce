<?php
namespace Pierce\Connection;

class User
{
    public $nick;
    public $username;
    public $host;
    public $realname;
    public $channels = [];
    public $server;
    public $hopcount;
    public $ircop = false;
    public $away = false;

    public function __construct($nick)
    {
        $this->nick = $nick;
    }

    public function __get($name)
    {
        if ($name == 'ident'):
            return $nick . '!' . $username . '@' . $host;
        endif;
    }
}
