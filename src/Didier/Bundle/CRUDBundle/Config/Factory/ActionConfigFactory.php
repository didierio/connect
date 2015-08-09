<?php

namespace Didier\Bundle\CRUDBundle\Config\Factory;

use Didier\Bundle\CRUDBundle\Config\ActionConfig;

abstract class ActionConfigFactory
{
    public static function create($name, array $config = array())
    {
        switch ($name) {
            case 'list':
                return Action\ListActionFactory::create($name, $config);
            case 'show':
                return Action\ShowActionFactory::create($name, $config);
            case 'create':
                return Action\CreateActionFactory::create($name, $config);
            case 'edit':
                return Action\EditActionFactory::create($name, $config);
            case 'delete':
                return Action\ListActionFactory::create($name, $config);
        }

        return Action\GenericActionFactory::create($name, $config);
    }
}
