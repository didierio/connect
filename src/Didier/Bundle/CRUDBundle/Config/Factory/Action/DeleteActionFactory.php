<?php

namespace Didier\Bundle\CRUDBundle\Config\Factory\Action;

abstract class DeleteActionFactory extends GenericActionFactory
{
    public static function create($name, array $config)
    {
        $config['controller'] = 'DidierCRUDBundle:CRUD:delete';
        $config['method'] = 'DELETE';
        $config['status_code'] = 200;

        return parent::create($name, $config);
    }
}
