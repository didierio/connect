<?php

namespace Didier\Bundle\OAuth2ServerBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="oauth2_access_token"
 * )
 */
class AccessToken extends BaseAccessToken
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="expires_at", type="integer", nullable=true)
     */
    protected $expiresAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $scope;

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
}
