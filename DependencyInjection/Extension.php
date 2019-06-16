<?php

namespace Webkul\UVDesk\ExtensionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as SymfonyExtension;

class Extension extends SymfonyExtension
{
    public function getAlias()
    {
        return 'uvdesk_extensions';
    }

    public function getConfiguration(array $configs, ContainerBuilder $container)
    {
        return new Configuration();
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        // Load bundle configurations
        $configuration = $this->getConfiguration($configs, $container);

        foreach ($this->processConfiguration($configuration, $configs) as $param => $value) {
            switch ($param) {
                case 'dir':
                    $container->setParameter("uvdesk_extensions.dir", $value);
                    break;
                default:
                    break;
            }
        }

        // Compile extensions
        $path = $container->getParameter('uvdesk_extensions.dir') . "/extensions.json";

        if (file_exists($path)) {
            $extensions = [];
            $extensionsJson = json_decode(file_get_contents($container->getParameter('uvdesk_extensions.dir') . "/extensions.json"), true);

            foreach ($extensionsJson['vendors'] as $vendor => $vendorAttributes) {
                foreach ($vendorAttributes['extensions'] as $vendorExtension => $vendorExtensionAttributes) {
                    if (!empty($vendorExtensionAttributes['ext'])) {
                        // Raise a warning to dump composer autoload if class is not found
                        // $extensions[] = new \ReflectionClass($vendorExtensionAttributes['ext']);
                    }
                }
            }
        }
    }
}
