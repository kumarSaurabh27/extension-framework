<?php

namespace Webkul\UVDesk\ExtensionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webkul\UVDesk\ExtensionBundle\DependencyInjection\Extensions;

class UVDeskExtensionBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new Extensions();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
