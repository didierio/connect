<?php

namespace Didier\Bundle\OAuth2ServerBundle\Entity;

use FOS\OAuthServerBundle\Entity\ClientManager as BaseClientManager;

class ClientManager extends BaseClientManager
{
    /**
     * {@inheritdoc}
     */
    public function findClientByPublicId($publicId)
    {
        return $this->findClientBy(array(
            'randomId'  => $publicId,
        ));
    }
}
