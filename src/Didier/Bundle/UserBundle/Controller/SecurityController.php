<?php

namespace Didier\Bundle\UserBundle\Controller;

use Didier\Bundle\UserBundle\Entity\User;
use Didier\Bundle\UserBundle\Form\Type\ProfilePasswordType;
use Didier\Bundle\UserBundle\Form\Type\ProfileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    /**
     * @Route("/register", name="didier_user_security_register")
     */
    public function registerAction(Request $request)
    {
        if (false === $this->container->getParameter('enable_registration', false)) {
            throw $this->createNotFoundException();
        }

        $user = new User();
        $form = $this->createForm(new ProfileType(), $user);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $encoder = $this->container->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $form->get('password')->getData());
            $user->setPassword($password);

            $em = $this->get('doctrine')->getManager();
            $em->persist($user);
            $em->flush($user);

            return $this->redirect($this->get('router')->generate('didier_user_security_login'));
        }

        return $this->render('DidierUserBundle:Security:register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/profile", name="didier_user_security_profile")
     */
    public function profileAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $user = $em->getRepository('Didier\Bundle\UserBundle\Entity\User')->find($this->getUser()->getId());

        $form = $this->createForm(new ProfileType(), $user);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->flush($user);

            $this->addFlash('success', 'Profile saved');

            return $this->redirect($this->get('router')->generate('didier_user_security_profile'));
        }

        return $this->render('DidierUserBundle:Security:profile.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/profile/password", name="didier_user_security_profile_password")
     */
    public function profilePasswordAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $user = $em->getRepository('Didier\Bundle\UserBundle\Entity\User')->find($this->getUser()->getId());

        $form = $this->createForm(new ProfilePasswordType(), $user);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $encoder = $this->container->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $form->get('password')->getData());
            $user->setPassword($password);

            $em->flush($user);

            $this->addFlash('success', 'Password changed');

            return $this->redirect($this->get('router')->generate('didier_user_security_profile_password'));
        }

        return $this->render('DidierUserBundle:Security:profile_password.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("/login", name="didier_user_security_login")
     */
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

        return $this->render('DidierUserBundle:Security:login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/login-check", name="didier_user_security_login_check")
     */
    public function loginCheckAction(Request $request)
    {
        throw $this->createNotFoundException();
    }
}
