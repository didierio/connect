<?php

namespace Didier\Bundle\OAuth2ServerBundle\Controller;

use Didier\Bundle\OAuth2ServerBundle\Entity\Client;
use Didier\Bundle\OAuth2ServerBundle\Form\Type\ClientType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends Controller
{
    /**
     * @Route("/oauth/v2/clients", name="didier_oauth2_server_client_list")
     */
    public function listAction()
    {
        $accessTokens = $this->get('doctrine')->getRepository('DidierOAuth2ServerBundle:AccessToken')->findAll();
        $authCodes = $this->get('doctrine')->getRepository('DidierOAuth2ServerBundle:AuthCode')->findAll();
        $clients = $this->get('doctrine')->getRepository('DidierOAuth2ServerBundle:Client')->findAll();
        $refreshTokens = $this->get('doctrine')->getRepository('DidierOAuth2ServerBundle:AuthCode')->findAll();

        return $this->render('DidierOAuth2ServerBundle:Client:list.html.twig', array(
            'accessTokens' => $accessTokens,
            'authCodes' => $authCodes,
            'clients' => $clients,
            'refreshTokens' => $refreshTokens,
            'now' => new \DateTime(),
        ));
    }

    /**
     * @Route("/oauth/v2/clients/create", name="didier_oauth2_server_client_create")
     */
    public function createAction(Request $request)
    {
        $client = new Client();
        $form = $this->createForm(new ClientType(), $client);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($client);
            $em->flush($client);

            return $this->redirect($this->get('router')->generate('didier_oauth2_server_client_list'));
        }

        return $this->render('DidierOAuth2ServerBundle:Client:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/oauth/v2/clients/{id}/edit", name="didier_oauth2_server_client_edit")
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

            return $this->redirect($this->get('router')->generate('didier_oauth2_server_client_list'));
        }

        return $this->render('DidierOAuth2ServerBundle:Client:edit.html.twig', array(
            'client' => $client,
            'form' => $form->createView(),
        ));
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

        return $this->render('DidierOAuth2ServerBundle:Security:login.html.twig', array(
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
