<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\DependencyInjection\Passes;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\RoutineInterface;

class ConfigureRoutines implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->has(Routine::class)) {
            $routineDefinition = $container->findDefinition(Routine::class);

            foreach ($container->findTaggedServiceIds(RoutineInterface::class) as $id => $tags) {
                $routineDefinition->addMethodCall('configureRoutine', array(new Reference($id), $tags));
            }
        }
    }
}
