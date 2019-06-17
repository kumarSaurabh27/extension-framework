<?php

namespace Webkul\UVDesk\ExtensionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Webkul\UVDesk\ExtensionBundle\Framework\HelpdeskModuleInterface;
use Webkul\UVDesk\ExtensionBundle\Framework\HelpdeskComponentInterface;

class Builder extends Extension
{
    public function getAlias()
    {
        return 'uvdesk_extensions';
    }

    public function getConfiguration(array $configs, ContainerBuilder $container)
    {
        return new BundleConfiguration();
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
                        $reflectedExtension = new \ReflectionClass($vendorExtensionAttributes['ext']);

                        if (!$reflectedExtension->implementsInterface(HelpdeskModuleInterface::class) && !$reflectedExtension->implementsInterface(HelpdeskComponentInterface::class)) {
                            throw new \Exception('Invalid extension type');
                        }

                        $extensions[] = $reflectedExtension;
                    }
                }
            }
        }

        // Load extension services
        // No routes will be loaded. Everything will be based on event driven arch.
        $loader->load("/home/users/akshay.kumar/Workstation/www/html/community-skeleton/apps/uvdesk/commons/Resources/config/services.yaml");
        $loader->load("/home/users/akshay.kumar/Workstation/www/html/community-skeleton/apps/uvdesk/ecommerce/Resources/config/services.yaml");
        $loader->load("/home/users/akshay.kumar/Workstation/www/html/community-skeleton/apps/uvdesk/shopify/Resources/config/services.yaml");
        
        // foreach ($extensions as $reflectedExtension) {
        //     $extension = $reflectedExtension->newInstanceWithoutConstructor();

        //     if ($extension::services() != null) {
        //         $dir = dirname($reflectedExtension->getFileName());

        //         foreach ($extension::services() as $relativePath) {
        //             dump($dir . $relativePath);

        //             $extensionsLoader->load($dir . $relativePath);
        //         }
        //     }
        // }
    }
}
