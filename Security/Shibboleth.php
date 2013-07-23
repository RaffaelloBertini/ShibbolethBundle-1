<?php

namespace Duke\ShibbolethBundle\Security;

use Symfony\Component\HttpFoundation\Request;

class Shibboleth 
{
	protected $login;
	protected $logout;
	protected $logoutTarget;
	    
    public function __construct($login, $logout, $logoutTarget) 
    {
    	$this->login = $login;
		$this->logout = $logout;
		$this->logoutTarget = $logoutTarget;
    }
	
	public function getLogin()
	{
		return $this->login;
	}
	
	public function getLogout()
	{
		return $this->logout;
	}	
	
	public function getLogoutTarget()
	{
		return $this->logoutTarget;
	}
}
