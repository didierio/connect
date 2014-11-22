<?php

namespace Didier\Bundle\OAuth2ServerBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *   name="oauth2_client"
 * )
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;  

    /**
     * @ORM\Column(name="random_id", type="string", length=255, nullable=false)
     */
    protected $randomId;

    /**
     * @ORM\Column(type="array", name="redirect_uris")
     */
    protected $redirectUris;

    /**
     * @ORM\Column(type="string")
     */
    protected $secret;

    /**
     * @ORM\Column(type="array", name="allowed_grant_types")
     */
    protected $allowedGrantTypes;

    /**
     * @ORM\ManyToOne(targetEntity="Didier\Bundle\UserBundle\Entity\User")
     */
    protected $user;

    public function getName()  
    {  
        return $this->name;  
    }  
  
    public function setName($name)  
    {  
        $this->name = $name;  
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getRedirectUri()
    {
        if (empty($this->redirectUris)) {
            return null;
        }

        return reset($this->redirectUris);
    }

    public function setRedirectUri($redirectUri)
    {
        $this->redirectUris = [$redirectUri];
    }

    public function getAllowedGrants()
    {
        return implode(', ', $this->allowedGrantTypes);
    }

    public function setAllowedGrants($allowedGrants = '')
    {
        $this->allowedGrantTypes = explode(', ', $allowedGrants);
    }
}
