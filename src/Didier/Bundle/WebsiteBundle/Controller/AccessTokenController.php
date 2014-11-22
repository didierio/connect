<?php

namespace Didier\Bundle\WebsiteBundle\Controller;

use Didier\Bundle\UserBundle\Entity\User;
use Didier\Bundle\UserBundle\Form\Type\RegistrationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class AccessTokenController extends Controller
{
    /**
     * @Route("/access_tokens", name="didier_website_access_token_list")
     */
    public function listAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        return $this->render('DidierWebsiteBundle:AccessToken:list.html.twig', [
            'accessTokens' => $this->getRepository()->findByUser($user),
            'now' => new \DateTime(),
        ]);
    }

    /**
     * @Route("/access_tokens/{id}/revoke", name="didier_website_access_token_revoke")
     */
    public function revokeAction(Request $request, $id)
    {
        $accessToken = $this->getRepository()->find($id);

        if (null === $accessToken) {
            throw $this->createNotFoundException('Token not found');
        }

        $em = $this->get('doctrine')->getManager();
        $em->remove($accessToken);
        $em->flush($accessToken);

        return $this->redirect($this->generateUrl('didier_website_access_token_list'));
    }

    private function getRepository()
    {
        return $this->get('doctrine')->getRepository('DidierOAuth2ServerBundle:AccessToken');
    }
}
