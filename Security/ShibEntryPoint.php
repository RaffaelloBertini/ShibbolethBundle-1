<?php

namespace Duke\ShibbolethBundle\Security;

use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Duke\ShibbolethBundle\Security\Shibboleth;

class ShibEntryPoint implements AuthenticationEntryPointInterface
{
    private $httpUtils;
	private $shib;

    public function __construct(HttpUtils $httpUtils, Shibboleth $shib)
    {
        $this->httpUtils = $httpUtils;
		$this->shib = $shib;
    }

	/**
	 * If the user needs to be authenticated, we are going to redirect them
	 * to the login path (e.g. /shibboleth) and append a 'returnto=<request URI>'
	 * query param. When the user returns back from his/her adventurous journey to 
	 * the IDP, we will know where to redirect them.
	 */
    public function start(Request $request, AuthenticationException $authException = null)
    {
		$relPath = $this->shib->getLogin(); // /shibboleth
		$host = $request->getHttpHost(); // symfony2:8080
		$baseUrl = $request->getBaseUrl(); // /app_dev.php
		
		$protocol = ($request->isSecure()) ? 'https://' : 'http://';
		//$returnUri = $request->getRequestUri();		
				
		$redirect = $protocol.$host.$baseUrl.$relPath;
		//$return = $protocol.$host.$returnUri;
		
		//$redirect = $redirect.'?returnto='.urlencode($return);

        //redirect action goes here
        return $this->httpUtils->createRedirectResponse($request, $redirect);
    }
}
