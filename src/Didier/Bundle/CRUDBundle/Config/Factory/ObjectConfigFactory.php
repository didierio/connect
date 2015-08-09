<?php

namespace Didier\Bundle\CRUDBundle\Config\Factory;

use Didier\Bundle\CRUDBundle\Config\ObjectConfig;

abstract class ObjectConfigFactory
{
    public static function create($name, array $config = array())
    {
        $actions = array();

        foreach ($config['actions'] as $key => $value) {
            $actions[$key] = ActionConfigFactory::create($key, $value);
        }

        return new ObjectConfig($name, $config['class'], $actions);
    }
}
