<?php

namespace Didier\Bundle\OAuth2ServerBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="oauth2_auth_code"
 * )
 */
class AuthCode extends BaseAuthCode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="Didier\Bundle\UserBundle\Entity\User")
     */
    protected $user;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $token;

    /**
     * @ORM\Column(type="string", name="redirect_uri")
     */
    protected $redirectUri;

    /**
     * @ORM\Column(name="expires_at", type="integer", nullable=true)
     */
    protected $expiresAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $scope;
}
