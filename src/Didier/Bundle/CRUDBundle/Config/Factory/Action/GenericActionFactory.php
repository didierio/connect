<?php

namespace Didier\Bundle\CRUDBundle\Config\Factory\Action;

use Didier\Bundle\CRUDBundle\Config\ActionConfig;

abstract class GenericActionFactory
{
    public static function create($name, array $config)
    {
        return new ActionConfig(
            $name,
            $config['method'],
            $config['controller'],
            $config['fields'],
            $config['role'],
            $config['status_code']
        );
    }
}
