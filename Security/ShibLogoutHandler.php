<?php

namespace Duke\ShibbolethBundle\Security;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Duke\ShibbolethBundle\Security\Shibboleth;

class ShibLogoutHandler implements LogoutHandlerInterface, LogoutSuccessHandlerInterface
{
	protected $shib;
	
	public function __construct(Shibboleth $shib)
	{
		$this->shib = $shib;
	}
	
    public function logout(Request $request, Response $response, TokenInterface $token)
    {	
        if ($token instanceof ShibUserToken) {
            $request->getSession()->invalidate();
        }        
    }
    
    public function onLogoutSuccess(Request $request)
    {
    	$target = $this->shib->getLogoutTarget();

        return new RedirectResponse($target);
    }    
}