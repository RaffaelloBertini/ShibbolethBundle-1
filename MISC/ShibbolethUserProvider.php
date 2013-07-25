<?php

// CHANGE ME
namespace YOUR\PROJECT\UserProvider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

// CHANGE ME
use YOUR\PROJECT\Document\User;

class ShibbolethUserProvider implements UserProviderInterface
{
    protected $dm;
	
    public function __construct($documentManager)
    {
        $this->dm = $documentManager;
    }
	
    public function loadUserByUsername($username)
    {
    	// CHANGE ME		
        $user = $this->dm->getRepository('<SOME BUNDLE>:<USER DOCUMENT>')->findOneByUsername($username);
		
        if (!$user) {
            $user = new User();
            $user->setUsername($username);
        }
        
        $this->dm->persist($user);
        $this->dm->flush();
        
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
    	// CHANGE ME
        return $class === 'YOUR\PROJECT\Document\User';
    }
}
