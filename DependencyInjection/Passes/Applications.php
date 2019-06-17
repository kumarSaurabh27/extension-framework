<?php

namespace Webkul\UVDesk\ExtensionBundle\DependencyInjection\Compilers\Extensions;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webkul\UVDesk\ExtensionBundle\Extensions\Application as ApplicationExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class Application implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ApplicationExtension::class)) {
            return;
        }

        $definition = $container->findDefinition(ApplicationExtension::class);

        foreach ($container->findTaggedServiceIds('uvdesk_extensions.application') as $id => $tags) {
            $definition->addMethodCall('addSegment', array(new Reference($id), $tags));
        }

        $definition->addMethodCall('organizeCollection');
    }
}
