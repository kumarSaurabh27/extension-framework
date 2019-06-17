<?php

namespace Webkul\UVDesk\ExtensionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webkul\UVDesk\ExtensionBundle\DependencyInjection\Passes;
use Webkul\UVDesk\ExtensionBundle\DependencyInjection\Builder;

class UVDeskExtensionBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new Builder();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new Passes\Packages());
    }
}
