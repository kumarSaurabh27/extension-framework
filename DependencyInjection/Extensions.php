<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\ExtensionManager;
use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\Application;
use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\ApplicationInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Module\ModuleInterface;

use Webkul\UVDesk\ExtensionFrameworkBundle\Package\Package;
use UVDesk\CommunityExtension\UVDesk\ShopifyModule\DependencyInjection\ShopifyConfiguration;

class Extensions extends Extension
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
        // $configuration = $this->getConfiguration($configs, $container);
        // $configuration = new BundleConfiguration();
        // $shopifyConfiguration = new ShopifyConfiguration();

        // dump($configs);
        // dump($configuration);
        // dump($shopifyConfiguration);

        // dump($this->processConfiguration($shopifyConfiguration, $configs));
        // die;

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

        // Automatically add service tags
        // $container->registerForAutoconfiguration(ModuleInterface::class)->addTag(ModuleInterface::class);

        // Compile apps
        $this->autoConfigureExtensions($container, $loader);
    }

    private function autoConfigureExtensions(ContainerBuilder $container, YamlFileLoader $loader) : Extensions
    {
        $lockfile = dirname($container->getParameter("uvdesk_extensions.dir")) . "/uvdesk.lock";

        if (!file_exists($lockfile) || !$container->has(ExtensionManager::class)) {
            return $this;
        }

        // $packages = Package::readPackagesFromLockFile($lockfile);
        $uvdesk = json_decode(file_get_contents($lockfile), true);
        $extensionManagerDefinition = $container->findDefinition(ExtensionManager::class);

        foreach ($uvdesk['packages'] as $attributes) {
            $extension = new \ReflectionClass($attributes['extension']);

            // The first thing we want to do is ensure that the services have been loaded
            foreach ($extension->getMethod('getServices')->invoke(null) as $resource) {
                $loader->load($resource);
            }

            // Override configuration and register extension with the extension manager
            $extensionDefinition = $container->findDefinition($extension->getName());
            $extensionDefinition
                ->setPrivate(true)
                ->setAutowired(false)
                ->setAutoconfigured(false)
                ->setArgument('$name', $attributes['name'])
                ->setArgument('$description', $attributes['description'])
                ->setArgument('$source', dirname($extension->getFileName()));
            
            $extensionManagerDefinition->addMethodCall('registerExtension', array(new Reference($extension->getName())));

            // Register available applications with the extension manager for auto init.
            foreach ($extension->getMethod('getApplications')->invoke(null) as $application) {
                $reflectedApplication = new \ReflectionClass($application);

                if ($reflectedApplication->isSubclassOf(Application::class)) {
                    $applicationDefinition = $container->findDefinition($application);
                    $applicationDefinition
                        ->setPrivate(true)
                        ->addMethodCall('setExtensionReference', [$extension->getName()]);

                    $extensionManagerDefinition->addMethodCall('registerApplication', array(new Reference($application)));
                }
            }
        }

        // Delegate further configuration to service upon init.
        $extensionManagerDefinition->addMethodCall('autoconfigure');

        return $this;
    }
}
