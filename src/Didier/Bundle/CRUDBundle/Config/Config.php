<?php

namespace Didier\Bundle\CRUDBundle\Config;

class Config
{
    private $objects;

    public function __construct(array $objects = array())
    {
        $this->objects = $objects;
    }

    public function getObjects()
    {
        return $this->objects;
    }

    public function getObject($name)
    {
        return $this->objects[$name];
    }
}
