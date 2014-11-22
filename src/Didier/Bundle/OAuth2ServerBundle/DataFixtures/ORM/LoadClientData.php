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
        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');

        $client = $clientManager->createClient();
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

    protected function setPassword(User $user, $password)
    {
        $factory = $this->container->get('security.encoder_factory');

        $encoder = $factory->getEncoder($user);
        $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
    }
}
