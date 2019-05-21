<?php

namespace Webkul\UVDesk\ExtensionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class HelpdeskExtension extends Extension
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

        // Compile packages
    }
}
