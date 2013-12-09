<?php

namespace Duke\ShibbolethBundle\Security;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Duke\ShibbolethBundle\Security\ShibbolethUserToken;

class ShibbolethAuthenticationProvider implements AuthenticationProviderInterface
{
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }	

    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());
        
        if ($user) {        	
            $authenticatedToken = new ShibbolethUserToken($user->getRoles());
            $authenticatedToken->setUser($user);
            
            return $authenticatedToken;
        }

        throw new AuthenticationException('Shib authentication failed.');
    }

    public function supports(TokenInterface $token)
    {
    	foreach ($token->getRoles() as $role) {
    		if ($role instanceof SwitchUserRole) {
    			$token = $role->getSource();
    			break;
    		}
    	}    	
    	
        return $token instanceof ShibbolethUserToken;
    }
}