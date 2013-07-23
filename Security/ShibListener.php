<?php

namespace Duke\ShibbolethBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Duke\ShibbolethBundle\Security\ShibUserToken;

class ShibListener implements ListenerInterface
{
    protected $securityContext;
	protected $authenticationManager;
	protected $authenticationEntryPoint;

    public function __construct(SecurityContextInterface $securityContext, 
    		AuthenticationManagerInterface $authenticationManager,
    		AuthenticationEntryPointInterface $authenticationEntryPoint)
    {
        $this->securityContext = $securityContext;
		$this->authenticationManager = $authenticationManager;
		$this->authenticationEntryPoint = $authenticationEntryPoint;		
    }

    public function handle(GetResponseEvent $event)
    {	
        $request = $event->getRequest();

        $remoteUser = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : 
            (isset($_SERVER['REDIRECT_REMOTE_USER']) ? $_SERVER['REDIRECT_REMOTE_USER'] : null);

		// If user is logging in, let's create that sweet sweet token
        if (!empty($remoteUser)) {
            $token = new ShibUserToken();
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
 		
		// If REMOTE_USER isn't set, check to see if user has an existing token. If not,
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
