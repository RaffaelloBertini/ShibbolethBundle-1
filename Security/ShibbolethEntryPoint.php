<?php

namespace Duke\ShibbolethBundle\Security;

use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Duke\ShibbolethBundle\Security\Shibboleth;

class ShibbolethEntryPoint implements AuthenticationEntryPointInterface
{
	private $shib;

    public function __construct(Shibboleth $shib)
    {
		$this->shib = $shib;
    }

	/**
	 * If the user needs to be authenticated, we are going to redirect them
	 * to the login path specified in the config.yml (e.g. /shibboleth)
	 */
    public function start(Request $request, AuthenticationException $authException = null)
    {
    	// There must be an easier way to create the URL? Why do I need to include
    	// $baseUrl (/app_dev.php). Shouldn't there be a method that create the host
    	// name with just that portion included?
		$relPath = $this->shib->getLogin();
		$host = $request->getHttpHost(); 
		$baseUrl = $request->getBaseUrl();
		$secure = ($request->isSecure()) ? 'https://' : 'http://'; 
				
		$redirect = $secure.$host.$baseUrl.$relPath;

        return new RedirectResponse($redirect);
    }
}
