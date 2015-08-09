<?php

namespace Didier\Bundle\CRUDBundle\Config\Factory;

use Didier\Bundle\CRUDBundle\Config\Config;

class ConfigFactory
{
    public static function create(array $config = array())
    {
        $objects = array();

        foreach ($config['objects'] as $key => $object) {
            $objects[$key] = ObjectConfigFactory::create($key, $object);
        }

        return new Config($objects);
    }
}
