<?php

namespace Didier\Bundle\OAuth2ServerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DidierOAuth2ServerBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSOAuthServerBundle';
    }
}
