<?php

namespace Didier\Bundle\CRUDBundle\Config;

class ActionConfig
{
    private $name;
    private $method;
    private $controller;
    private $fields;
    private $role;
    private $statusCode;

    public function __construct($name, $method, $controller = null, array $fields = array(), $role = null, $statusCode = 200)
    {
        $this->name = $name;
        $this->method = $method;
        $this->controller = $controller;
        $this->fields = $fields;
        $this->role = $role;
        $this->statusCode = $statusCode;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getRole()
    {
        return $this->role;
    }
}
