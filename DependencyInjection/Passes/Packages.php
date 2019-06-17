<?php

namespace Webkul\UVDesk\ExtensionBundle\DependencyInjection\Passes;

use Webkul\UVDesk\ExtensionBundle\App\Configuration;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Compiler Pass: Extensibles
 * 
 * This registers all availabe extensibles via Extensibles Builder.
 * 
 * Developers can create your own extensibles by creating a service implementing the 
 * ExtendableComponentInterface interface.
 */
class Packages implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(Configuration::class)) {
            return;
        }

        $definition = $container->findDefinition(Configuration::class);

        foreach ($container->findTaggedServiceIds('uvdesk_extensions.app_extension') as $serviceId => $serviceTags) {
            $definition->addMethodCall('registerExtension', array(new Reference($serviceId), $serviceTags));
        }

        // This will be called as soon as the service is initialized
        $definition->addMethodCall('configure');
    }
}
