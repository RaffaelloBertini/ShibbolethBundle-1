<?php

namespace Fahl\ShibbolethBundle\Security;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class ShibbolethFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {			
        $providerId = 'security.authentication.provider.shibboleth.'.$id;
        $container
            ->setDefinition($providerId, 
            	new DefinitionDecorator('shibboleth.security.authentication.provider'))
			->addArgument(new Reference($userProvider));
		
        $entryPointId = 'security.entry_point.shibboleth.'.$id;
        $container
            ->setDefinition($entryPointId, 
            	new DefinitionDecorator('shibboleth.security.entry_point'));
		
        $listenerId = 'security.authentication.listener.shibboleth.'.$id;
        $container
        	->setDefinition($listenerId, 
        		new DefinitionDecorator('shibboleth.security.authentication.listener'))
			->replaceArgument(2, new Reference($entryPointId));				
		
        return array($providerId, $listenerId, $entryPointId);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'shibboleth';
    }

    public function addConfiguration(NodeDefinition $node) {}
}