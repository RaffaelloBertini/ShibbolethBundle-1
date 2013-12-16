<?php

namespace Duke\ShibbolethBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

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
		
		// Check for user authorization variable
		if ($usernameVar === 'REMOTE_USER') {
			// I discovered this by pure luck. When using the production controller,
			// the REDIRECT_REMOTE_USER gets set, so my initial logic would fail and
			// create a redirect loop since I was just looking for the REMOTE_USER.
			// I don't understand the dynamics of this yet, but it seems to have fixed
			// the problem.
			// TODO Learn dynamics of REDIRECT/REMOTE_USER
			
			$remoteUser = (isset($_SERVER['REDIRECT_REMOTE_USER'])) ? 
				$_SERVER['REDIRECT_REMOTE_USER'] : $remoteUser = (isset($_SERVER[$usernameVar])) ? $_SERVER[$usernameVar] : false;		
		} else {
        	$remoteUser = isset($_SERVER[$usernameVar]) ? $_SERVER[$usernameVar] : false;
		}
		
		// If user is logging in, let's create that sweet sweet token.
		// Otherwise, check for existing token.
        if (!empty($remoteUser)) {
            $token = new ShibbolethUserToken();
            $token->setUser($remoteUser);
        } else {
        	$token = $this->securityContext->getToken();
        } 


        
        // If token exists authenticate it. Else,
        // kick them off to the login URL. Special thanks to the poster 'm2mdas' for demonstrating
        // how to do this. The Symfony docs provide no information on how to implement a redirect
        // http://stackoverflow.com/questions/10089816/symfony2-how-to-check-if-an-action-is-secured        
        if (!empty($token)) {
        	try {
        		$authToken = $this->authenticationManager->authenticate($token);
        		$this->securityContext->setToken($authToken);

        		return;	
        	} catch (AuthenticationException $e) {
        	}
        } else {  	
        	$response = $this->authenticationEntryPoint->start($request);
        	$event->setResponse($response);        	
        }
    }
}
