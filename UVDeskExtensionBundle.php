<?php

namespace Webkul\UVDesk\ExtensionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webkul\UVDesk\ExtensionBundle\DependencyInjection\Extension;

class UVDeskExtensionBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new Extension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
