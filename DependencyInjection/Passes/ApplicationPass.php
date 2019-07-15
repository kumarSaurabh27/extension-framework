<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\DependencyInjection\Passes;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application\ApplicationInterface;

class ApplicationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        dump('configuring applications');
        // if ($container->has(RouteLoader::class)) {
        //     $router = $container->findDefinition(RouteLoader::class);

        //     foreach ($container->findTaggedServiceIds(RoutingResourceInterface::class) as $id => $tags) {
        //         $class = new \ReflectionClass($id);

        //         if ($class->implementsInterface(ExposedRoutingResourceInterface::class)) {
        //             $router->addMethodCall('addExposedRoutingResource', array(new Reference($id), $tags));
        //         } else if ($class->implementsInterface(ProtectedRoutingResourceInterface::class)) {
        //             $router->addMethodCall('addProtectedRoutingResource', array(new Reference($id), $tags));
        //         }
        //     }
        // }
    }
}
