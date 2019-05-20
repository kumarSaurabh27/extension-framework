<?php

namespace Webkul\UVDesk\ExtensionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webkul\UVDesk\ExtensionBundle\DependencyInjection\HelpdeskExtension;

class UVDeskExtensionBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new HelpdeskExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
