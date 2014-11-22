<?php

namespace Didier\Bundle\OAuth2ServerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{
    const CLIENT_NAME = 'didierConnectTest';

    public function userAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        return new JsonResponse(array(
            'id' => $user->getId(),
            'email' => $user->getemail(),
            'name' => $user->getUsername(),
        ));
    }

    public function linksAction()
    {
        $client = $this->get('doctrine')->getRepository('DidierOAuth2ServerBundle:Client')->findOneByName(self::CLIENT_NAME);
        $route = $this->get('router')->generate('didier_oauth2_server_default_links', array(), UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->render('DidierOAuth2ServerBundle:Default:links.html.twig', array(
            'client' => $client,
            'route' => $route,
        ));
    }

    public function apiAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }

        die('prout');
    }

    public function createClientAction(Request $request)
    {
        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');  
        $route = $this->get('router')->generate('didier_oauth2_server_default_links', array(), UrlGeneratorInterface::ABSOLUTE_URL);

        $client = $clientManager->createClient();
        $client->setName(self::CLIENT_NAME);
        $client->setRedirectUris(array($route));
        $client->setAllowedGrantTypes(array('token', 'authorization_code'));

        $clientManager->updateClient($client);

        return $this->redirect($route);
    }
}
