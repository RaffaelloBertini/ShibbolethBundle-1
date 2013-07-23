<?php

namespace Duke\ShibbolethBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Duke\ShibbolethBundle\Security\ShibFactory;

class ShibbolethBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ShibFactory());
    }	
}
