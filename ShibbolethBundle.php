<?php

namespace Fahl\ShibbolethBundle;

use Fahl\ShibbolethBundle\Security\ShibbolethFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ShibbolethBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ShibbolethFactory());
    }
}
