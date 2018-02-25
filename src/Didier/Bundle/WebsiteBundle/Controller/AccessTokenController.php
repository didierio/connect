<?php

namespace Didier\Bundle\WebsiteBundle\Controller;

use Didier\Bundle\OAuth2ServerBundle\Entity\AccessToken;
use Didier\Bundle\OAuth2ServerBundle\Form\Type\AccessTokenType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AccessTokenController extends Controller
{
    /**
     * @Route("/access-tokens", name="didier_website_access_token_list")
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
     * @Route("/access-tokens/{id}", name="didier_website_access_token_show")
     */
    public function showAction(Request $request, $id)
    {
        $manager = $this->get('fos_oauth_server.access_token_manager.default');
        $accessToken = $manager->findTokenBy(['id' => $id]);

        if (null === $accessToken) {
            throw $this->createNotFoundException(sprintf('AccessToken #%d not found', $id));
        }

        return $this->render('DidierWebsiteBundle:AccessToken:show.html.twig', array(
            'accessToken' => $accessToken,
            'now' => new \DateTime(),
        ));
    }

    /**
     * @Route("/access-tokens/{id}/edit", name="didier_website_access_token_edit")
     */
    public function editAction(Request $request, $id)
    {
        $manager = $this->get('fos_oauth_server.access_token_manager.default');
        $accessToken = $manager->findTokenBy(['id' => $id]);

        if (null === $accessToken) {
            throw $this->createNotFoundException(sprintf('AccessToken #%d not found', $id));
        }

        $form = $this->createForm(new AccessTokenType(), $accessToken);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $manager->updateToken($accessToken);

            return $this->redirect($this->get('router')->generate('didier_website_access_token_show', ['id' => $accessToken->getId()]));
        }

        return $this->render('DidierWebsiteBundle:AccessToken:edit.html.twig', array(
            'form' => $form->createView(),
            'accessToken' => $accessToken,
        ));
    }

    /**
     * @Route("/access-tokens/{id}/revoke", name="didier_website_access_token_revoke")
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
