<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webkul\UVDesk\ExtensionFrameworkBundle\DependencyInjection\ContainerExtension;
use Webkul\UVDesk\ExtensionFrameworkBundle\DependencyInjection\Passes\RoutingConfigurator;

class ExtensionFrameworkBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ContainerExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RoutingConfigurator());
    }
}
