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
}
