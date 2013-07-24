<?php

namespace <SOME NAMESPACE>\UserProvider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use <SOME NAMESPACE>\Document\User;

class ShibUserProvider implements UserProviderInterface
{
    protected $dm;
	
    public function __construct($documentManager)
    {
        $this->dm = $documentManager;
    }
	
    public function loadUserByUsername($username)
    {		
        $user = $this->dm->getRepository('<SOME BUNDLE>:User')->findOneByUsername($username);
		
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
        return $class === '<SOME NAMESPACE>\Document\User';
    }
}
