<?php

namespace Didier\Bundle\WebsiteBundle\Controller;

use Didier\Bundle\OAuth2ServerBundle\Entity\Client;
use Didier\Bundle\OAuth2ServerBundle\Form\Type\ClientType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        $client = new Client();
        $client->setUser($this->get('security.context')->getToken()->getUser());
        $form = $this->createForm(new ClientType(), $client);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($client);
            $em->flush($client);

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

            return $this->redirect($this->get('router')->generate('didier_website_client_list'));
        }

        return $this->render('DidierWebsiteBundle:Client:edit.html.twig', array(
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
