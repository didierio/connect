<?php

namespace Didier\Bundle\CRUDBundle\Config;

class ObjectConfig
{
    private $name;
    private $class;
    private $actions;

    public function __construct($name, $class, array $actions = array())
    {
        $this->name = $name;
        $this->class = $class;
        $this->actions = $actions;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function hasAction($name)
    {
        return array_key_exists($name, $this->actions);
    }

    public function getAction($name)
    {
        return $this->actions[$name];
    }
}
