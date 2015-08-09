<?php

namespace Didier\Bundle\CRUDBundle\Config\Factory\Action;

abstract class ShowActionFactory extends GenericActionFactory
{
    public static function create($name, array $config)
    {
        $config['controller'] = 'DidierCRUDBundle:CRUD:show';
        $config['method'] = 'GET';
        $config['status_code'] = 200;

        return parent::create($name, $config);
    }
}
