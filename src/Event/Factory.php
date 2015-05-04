<?php
namespace Pierce\Event;

class Factory implements \Pierce\FactoryInterface
{
    private $type;

    public function __construct($t = '')
    {
        $this->type = $t;
    }

    public function __set($name, $val)
    {
        $this->$name = $val;
    }

    public function create()
    {
        if (empty($this->type)):
            $classname = 'Noair\Event';
        else:
            $classname = 'Pierce\Event\\' . $this->type . 'Event';
        endif;

        return (new \ReflectionClass($classname))->newInstanceArgs(func_get_args());
    }
}
