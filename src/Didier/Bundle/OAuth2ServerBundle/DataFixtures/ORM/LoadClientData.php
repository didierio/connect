<?php

namespace Didier\Bundle\OAuth2ServerBundle\Entity\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadClientData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * Service container of the application
     *
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @inheritdoc
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $clientManager = $this->container->get('didier_oauth2_server.client_manager.default');

        $client = $clientManager->createClient();
        $client->setUser($this->getReference('user-julien'));
        $client->setName('www');
        $client->setRedirectUris(array('http://connect.didier.io'));
        $client->setAllowedGrantTypes(array('token', 'authorization_code'));

        $clientManager->updateClient($client);
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 2;
    }
}
