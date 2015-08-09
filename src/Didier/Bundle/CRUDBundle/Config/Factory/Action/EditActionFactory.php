<?php

namespace Didier\Bundle\CRUDBundle\Config\Factory\Action;

abstract class EditActionFactory extends GenericActionFactory
{
    public static function create($name, array $config)
    {
        $config['controller'] = 'DidierCRUDBundle:CRUD:edit';
        $config['method'] = 'PUT';
        $config['status_code'] = 200;

        return parent::create($name, $config);
    }
}
