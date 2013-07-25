<?php

namespace Duke\ShibbolethBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Duke\ShibbolethBundle\Security\Shibboleth;
use Duke\ShibbolethBundle\Security\ShibbolethUserToken;

class ShibbolethListener implements ListenerInterface
{
    protected $securityContext;
	protected $authenticationManager;
	protected $authenticationEntryPoint;
	protected $shib;

    public function __construct(SecurityContextInterface $securityContext, 
    		AuthenticationManagerInterface $authenticationManager,
    		AuthenticationEntryPointInterface $authenticationEntryPoint, Shibboleth $shib)
    {
        $this->securityContext = $securityContext;
		$this->authenticationManager = $authenticationManager;
		$this->authenticationEntryPoint = $authenticationEntryPoint;
		$this->shib = $shib;		
    }

    public function handle(GetResponseEvent $event)
    {	
        $request = $event->getRequest();
		$usernameVar = $this->shib->getUsername();

        $remoteUser = isset($_SERVER[$usernameVar]) ? $_SERVER[$usernameVar] : false;

		// If user is logging in, let's create that sweet sweet token
        if (!empty($remoteUser)) {
            $token = new ShibbolethUserToken();
            $token->setUser($remoteUser);

            try {
                $authToken = $this->authenticationManager->authenticate($token);
				$this->securityContext->setToken($authToken);

				return;
            } catch (AuthenticationException $e) {
            	
                // you might log something here
            }
        } 

		// Default Symfony response to denying authorization
		//$response = new Response();
		//$response->setStatusCode(403);
		//$event->setResponse($response);
 		
		// If $usernameVar isn't set, check to see if user has an existing token. If not,
		// kick them to login URL. Special thanks to the poster 'm2mdas' for demonstrating
		// how to do this. The Symfony docs provide no information on how to implement a 
		// redirect
		// http://stackoverflow.com/questions/10089816/symfony2-how-to-check-if-an-action-is-secured

		$existing = $this->securityContext->getToken();
		
		if (empty($remoteUser) && empty($existing)) {
			$response = $this->authenticationEntryPoint->start($request);
            $event->setResponse($response);
		}
		
		return;
    }
}
