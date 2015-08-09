<?php

namespace Didier\Bundle\CRUDBundle\Routing;

use Didier\Bundle\CRUDBundle\Config\Config;
use Didier\Bundle\CRUDBundle\Config\ObjectConfig;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouterLoader implements LoaderInterface
{
    private $config;
    private $loaded;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $routes = new RouteCollection();

        $route = new Route('/', [
            '_controller' => 'FrameworkBundle:Template:template',
            'template' => 'DidierCRUDBundle::layout.html.twig',
        ], [], [], null, null, array('GET'));

        $routes->add('crud_index', $route);

        $this->loadObjects($routes);

        $this->loaded = true;

        return $routes;
    }

    private function loadObjects(RouteCollection $routes)
    {
        foreach ($this->config->getObjects() as $name => $object) {
            $this->loadActions($routes, $object);
        }
    }

    private function loadActions(RouteCollection $routes, ObjectConfig $object)
    {
        foreach ($object->getActions() as $action) {
            $path = sprintf('/%s/%s', $object->getName(), $action->getName());
            $routeName = sprintf('crud_%s_%s', $object->getName(), $action->getName());
            $requirements = [];

            $defaults = array(
                '_controller' => $action->getController(),
                '_object' => $object->getName(),
                '_class' => $object->getClass(),
                '_action' => $action->getName(),
            );

            if ($action->getName() !== 'list' && $action->getName() !== 'create') {
                $path = sprintf('/%s/{id}/%s', $object->getName(), $action->getName());
                $requirements['id'] = '\d+';
            }

            if ($action->getName() === 'list') {
                $path = sprintf('/%s', $object->getName());
            }

            $route = new Route($path, $defaults, $requirements, [], null, null, array($action->getMethod()));
            $routes->add($routeName, $route);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'didier_crud' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
