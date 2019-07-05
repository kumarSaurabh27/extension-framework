<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webkul\UVDesk\ExtensionFrameworkBundle\DependencyInjection\BundleExtension;

class ExtensionFrameworkBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new BundleExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DependencyInjection\Passes\ConfigureRoutines());
    }
}
