<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\DependencyInjection\Passes;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\MappingResource;
use Webkul\UVDesk\ExtensionFrameworkBundle\Configurators\Configurator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package\PackageInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application\ApplicationInterface;

class TwigPaths implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->has('uvdesk_extension.twig_loader') && $container->has(MappingResource::class)) {
            $twig = $container->get('uvdesk_extension.twig_loader');
            $mappingResource = $container->get(MappingResource::class);

            foreach ($mappingResource->getPackages() as $id => $attributes) {
                $class = new \ReflectionClass($id);
                $resources = dirname($class->getFileName()) . "/Resources/views";

                list($vendor, $package) = explode('/', $attributes['metadata']['name']);

                if (is_dir($resources)) {
                    $twig->addPath($resources, sprintf("_uvdesk_extension_%s_%s", $vendor, $package));
                }
            }
        }
    }
}
