<?php 

namespace Fahl\ShibbolethBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class ShibbolethUserToken extends AbstractToken
{
    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        $this->setAuthenticated(count($roles) > 0);
    }

    public function getCredentials()
    {
        return '';
    }
}