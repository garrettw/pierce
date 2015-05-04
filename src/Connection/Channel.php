<?php
namespace Pierce\Connection;

class Channel
{
    public $name = '';
    public $key = '';
    public $mode = '';
    public $topic = '';
    private $users = ['founder' => [], 'admin' => [], 'op' => [],
        'hop' => [], 'voice' => [], 'none' => []];
    private $banlist = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function addUser(User $user, $mode = 'none')
    {
        if (!in_array($user, $this->users[$mode])):
            $user->channels[$this->name] = $mode;
            $this->users[$mode][] = $user;
        endif;
        return $this;
    }

    public function removeUser($ident)
    {
        foreach ($this->users as $mode => $list):
            foreach ($list as $i => $user):
                if ($user->ident == $ident):
                    unset($this->users[$mode][$i]);
                    return true;
                endif;
            endforeach;
        endforeach;
        return false;
    }
}
