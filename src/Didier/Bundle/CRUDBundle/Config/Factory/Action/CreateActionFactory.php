<?php

namespace Didier\Bundle\CRUDBundle\Config\Factory\Action;

abstract class CreateActionFactory extends GenericActionFactory
{
    public static function create($name, array $config)
    {
        $config['controller'] = 'DidierCRUDBundle:CRUD:create';
        $config['method'] = 'POST';
        $config['status_code'] = 201;

        return parent::create($name, $config);
    }
}
