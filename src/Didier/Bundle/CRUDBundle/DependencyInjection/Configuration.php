<?php

namespace Didier\Bundle\CRUDBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('didier_crud');

        $this->addObjectsSection($rootNode);

        return $treeBuilder;
    }

    private function addObjectsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('prefix')->defaultValue('/')->end()
                ->arrayNode('objects')
                    ->useAttributeAsKey('object')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')->end()
                            ->scalarNode('connector')->end()
                            ->append($this->getActionsConfiguration())
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function getActionsConfiguration()
    {
        $node = new ArrayNodeDefinition('actions');

        return $node
            ->useAttributeAsKey('action')
            ->prototype('array')
                ->children()
                    ->scalarNode('role')->end()
                    ->scalarNode('controller')->defaultNull()->end()
                    ->append($this->getFieldsConfiguration())
                ->end()
            ->end()
        ;
    }

    private function getFieldsConfiguration()
    {
        $node = new ArrayNodeDefinition('fields');

        return $node
            ->useAttributeAsKey('field')
            ->prototype('scalar')->end()
        ;
    }
}
