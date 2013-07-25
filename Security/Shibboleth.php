<?php

namespace Fahl\ShibbolethBundle\Security;

use Symfony\Component\HttpFoundation\Request;

class Shibboleth 
{
	protected $login;
	protected $logout;
	protected $returnTo;
	protected $username;
	    
    public function __construct($login, $logout, $returnTo, $username) 
    {
    	$this->login = $login;
		$this->logout = $logout;
		$this->returnTo = $returnTo;
		$this->username = $username;
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
	
	public function getUsername()
	{
		return $this->username;
	}
}
