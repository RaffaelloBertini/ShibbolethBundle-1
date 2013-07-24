<?php

namespace Duke\ShibbolethBundle\Security;

use Symfony\Component\HttpFoundation\Request;

class Shibboleth 
{
	protected $login;
	protected $logout;
	protected $returnTo;
	    
    public function __construct($login, $logout, $returnTo) 
    {
    	$this->login = $login;
		$this->logout = $logout;
		$this->returnTo = $returnTo;
    }
	
	public function getLogin()
	{
		return $this->login;
	}
	
	public function getLogout()
	{
		return $this->logout;
	}	
	
	public function getReturnTo()
	{
		return $this->returnTo;
	}
}
