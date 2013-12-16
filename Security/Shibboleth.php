<?php

namespace Duke\ShibbolethBundle\Security;

use Symfony\Component\HttpFoundation\Request;

class Shibboleth 
{
	private $login;
	private $logout;
	private $returnTo;
	private $username;
	    
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
