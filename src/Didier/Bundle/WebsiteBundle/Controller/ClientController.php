<?php

namespace Didier\Bundle\WebsiteBundle\Controller;

use Didier\Bundle\OAuth2ServerBundle\Entity\Client;
use Didier\Bundle\OAuth2ServerBundle\Form\Type\ClientType;
use GuzzleHttp\Client as GuzzleClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ClientController extends Controller
{
    /**
     * @Route("/clients", name="didier_website_client_list")
     */
    public function listAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $clients = $this->get('doctrine')->getRepository('DidierOAuth2ServerBundle:Client')->findByUser($user);

        return $this->render('DidierWebsiteBundle:Client:list.html.twig', array(
            'accessTokens' => array(),
            'authCodes' => array(),
            'clients' => $clients,
            'refreshTokens' => array(),
            'now' => new \DateTime(),
        ));
    }

    /**
     * @Route("/clients/create", name="didier_website_client_create")
     */
    public function createAction(Request $request)
    {
        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setAllowedGrantTypes(array('token', 'authorization_code'));

        $client->setUser($this->get('security.context')->getToken()->getUser());
        $form = $this->createForm(new ClientType(), $client);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $clientManager->updateClient($client);

            return $this->redirect($this->get('router')->generate('didier_website_client_list'));
        }

        return $this->render('DidierWebsiteBundle:Client:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/clients/{id}/edit", name="didier_website_client_edit")
     */
    public function editAction(Request $request, $id)
    {
        $client = $this->get('doctrine')->getRepository('DidierOAuth2ServerBundle:Client')->find($id);

        if (null === $client) {
            throw $this->createNotFoundException(sprintf('Client #%d not found', $id));
        }

        $form = $this->createForm(new ClientType(), $client);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->flush($client);

            return $this->redirect($this->get('router')->generate('didier_website_client_edit', ['id' => $id]));
        }

        return $this->render('DidierWebsiteBundle:Client:edit.html.twig', array(
            'client' => $client,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/clients/{id}/create-token", name="didier_website_client_create_token")
     */
    public function createTokenAction(Request $request, $id)
    {
        $doctrine = $this->get('doctrine');
        $client = $doctrine->getRepository('DidierOAuth2ServerBundle:Client')->find($id);

        if (null === $client) {
            throw $this->createNotFoundException(sprintf('Client #%d not found', $id));
        }

        $router = $this->get('router');
        $redirectUri = $router->generate('didier_website_client_generate_token', [
            'id' => $client->getId(),
        ], true);
        $client->addRedirectUri($redirectUri);
        $doctrine->getManager()->flush($client);

        return $this->redirect($router->generate('fos_oauth_server_authorize', [
            'client_id' => $client->getRandomId(),
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
        ]));
    }

    /**
     * @Route("/clients/{id}/generate-token", name="didier_website_client_generate_token")
     */
    public function generateTokenAction(Request $request, $id)
    {
        $doctrine = $this->get('doctrine');
        $client = $doctrine->getRepository('DidierOAuth2ServerBundle:Client')->find($id);

        if (null === $client) {
            throw $this->createNotFoundException(sprintf('Client #%d not found', $id));
        }

        if (null === $code = $request->query->get('code')) {
            throw new BadRequestHttpException('Query parameter "code" not found on request');
        }

        $router = $this->get('router');
        $redirectUri = $router->generate('didier_website_client_generate_token', [
            'id' => $client->getId(),
        ], true);

        try {
            $guzzleClient = new GuzzleClient();
            $guzzleClient->get($router->generate('fos_oauth_server_token', [
                'client_id' => $client->getRandomId(),
                'client_secret' => $client->getSecret(),
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ], true));
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', sprintf('%s', $e->getMessage()));

            return $this->redirect($router->generate('didier_website_client_edit', ['id' => $id]));
        }

        $redirectUris = $client->getRedirectUris();
        unset($redirectUris[array_search($redirectUri, $redirectUris)]);
        $client->setRedirectUris($redirectUris);
        $doctrine->getManager()->flush($client);

        return $this->redirect($router->generate('didier_website_access_token_list'));
    }

    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            $error = $error->getMessage(); // WARNING! Symfony source code identifies this line as a potential security threat.
        }

        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        return $this->render('DidierWebsiteBundle:Security:login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    public function loginCheckAction(Request $request)
    {
        throw $this->createNotFoundException();
    }

    public function revokeAction(Request $request)
    {
        $token = $request->query->get('token');
        if (null === $token) {
            throw $this->createAccessDeniedException('No token found in the request query parameters');
        }

        $doctrine = $this->get('doctrine');
        $accessToken = $doctrine->getRepository('DidierOAuth2ServerBundle:AccessToken')->findOneByToken($token);

        if (null === $accessToken) {
            throw $this->createNotFoundException('Token not found');
        }

        $em = $doctrine->getManager();
        $em->remove($accessToken);
        $em->flush($accessToken);

        return new JsonResponse(array('ok'));
    }
}
