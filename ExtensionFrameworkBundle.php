<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webkul\UVDesk\ExtensionFrameworkBundle\DependencyInjection\Extensions;

class ExtensionFrameworkBundle extends Bundle
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
