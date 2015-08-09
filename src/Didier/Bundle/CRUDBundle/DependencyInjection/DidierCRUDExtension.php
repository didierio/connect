<?php

namespace Didier\Bundle\CRUDBundle\DependencyInjection;

use Didier\Bundle\CRUDBundle\Config\Factory\ConfigFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DidierCRUDExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setDefinition('didier_crud.config_factory', new Definition(
            'Didier\Bundle\CRUDBundle\Config\Factory\ConfigFactory'
        ));

        $container
            ->setDefinition('didier_crud.config', new Definition(
                'Didier\Bundle\CRUDBundle\Config\Config',
                array($config)
            ))
            ->setFactoryService('didier_crud.config_factory')
            ->setFactoryMethod('create')
        ;
    }
}
