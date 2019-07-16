<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\DependencyInjection\Passes;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\MappingResource;
use Webkul\UVDesk\ExtensionFrameworkBundle\Configurators\Configurator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package\PackageInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application\ApplicationInterface;

class ConfigurationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->has(MappingResource::class)) {
            $configurator = new Reference(Configurator::class);
            $mappingResource = $container->findDefinition(MappingResource::class);

            foreach ($container->findTaggedServiceIds(PackageInterface::class) as $reference => $tags) {
                $mappingResource->addMethodCall('setPackage', array($reference, $tags));

                $packageDefinition = $container->findDefinition($reference);
                $packageDefinition->setConfigurator([$configurator, 'configurePackage']);
            }

            foreach ($container->findTaggedServiceIds(ApplicationInterface::class) as $reference => $tags) {
                $mappingResource->addMethodCall('setApplication', array($reference, $tags));

                $applicationDefinition = $container->findDefinition($reference);
                $applicationDefinition->setConfigurator([$configurator, 'configureApplication']);
            }

            $container->findDefinition(Configurator::class)->addMethodCall('autoconfigure');
        }
    }
}
