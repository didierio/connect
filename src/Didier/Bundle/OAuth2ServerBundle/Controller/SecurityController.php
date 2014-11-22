<?php

namespace Didier\Bundle\OAuth2ServerBundle\Controller;

use Didier\Bundle\UserBundle\Entity\User;
use Didier\Bundle\UserBundle\Form\Type\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new RegistrationType(), $user);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            $password = $encoder->encodePassword($form->get('password')->getData(), $user->getSalt());
            $user->setPassword($password);

            $em = $this->get('doctrine')->getManager();
            $em->persist($user);
            $em->flush($user);

            return $this->redirect($this->get('router')->generate('didier_oauth2_server_security_login'));
        }

        return $this->render('DidierOAuth2ServerBundle:Security:register.html.twig', array(
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
