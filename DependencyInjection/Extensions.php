<?php

namespace Webkul\UVDesk\ExtensionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Webkul\UVDesk\ExtensionBundle\Framework\ExtensionManager;
use Webkul\UVDesk\ExtensionBundle\Framework\Application;
use Webkul\UVDesk\ExtensionBundle\Framework\ApplicationInterface;
use Webkul\UVDesk\ExtensionBundle\Framework\ModuleInterface;

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
        $path = $container->getParameter('uvdesk_extensions.dir') . "/extensions.json";
        
        if (file_exists($path)) {
            $this
                ->compileExtensions($path)
                ->autoConfigureExtensions($container, $loader);
        }
    }

    private function compileExtensions($path) : Extensions
    {
        $json = json_decode(file_get_contents($path), true);

        foreach ($json['vendors'] as $vendor => $attributes) {
            foreach ($attributes['extensions'] as $package => $extensionConfiguration) {
                $reflectedConfiguration = new \ReflectionClass($extensionConfiguration);

                if (!$reflectedConfiguration->implementsInterface(ModuleInterface::class)) {
                    $message = "Extension %s/%s [%s] is not supported.";

                    throw new \Exception(sprintf($message, $vendor, $extension, $reflectedConfiguration->getName())); 
                }

                $this->collection[] = [
                    'vendor' => $vendor,
                    'package' => $package,
                    'configuration' => $reflectedConfiguration,
                ];
            }
        }

        return $this;
    }

    private function autoConfigureExtensions(ContainerBuilder $container, YamlFileLoader $loader) : Extensions
    {
        if ($container->has(ExtensionManager::class)) {
            $extensionManagerDefinition = $container->findDefinition(ExtensionManager::class);
        
            foreach ($this->collection as $attributes) {
                $reflectedExtension = $attributes['configuration'];

                // The first thing we want to do is ensure that the services have been loaded
                foreach ($reflectedExtension->getMethod('getServices')->invoke(null) as $resource) {
                    $loader->load($resource);
                }

                // Override configuration and register extension with the extension manager
                $extensionDefinition = $container->findDefinition($reflectedExtension->getName());
                $extensionDefinition
                    ->setPrivate(true)
                    ->setAutowired(false)
                    ->setAutoconfigured(false)
                    ->setArgument('$vendor', $attributes['vendor'])
                    ->setArgument('$package', $attributes['package'])
                    ->setArgument('$directory', dirname($reflectedExtension->getFileName()));
                
                $extensionManagerDefinition->addMethodCall('registerExtension', array(new Reference($reflectedExtension->getName())));

                // Register available applications with the extension manager for auto init.
                foreach ($reflectedExtension->getMethod('getApplications')->invoke(null) as $application) {
                    $reflectedApplication = new \ReflectionClass($application);

                    if ($reflectedApplication->isSubclassOf(Application::class)) {
                        $applicationDefinition = $container->findDefinition($application);
                        $applicationDefinition
                            ->setPrivate(true)
                            ->addMethodCall('setExtensionReference', [$reflectedExtension->getName()]);

                        $extensionManagerDefinition->addMethodCall('registerApplication', array(new Reference($application)));
                    }
                }
            }

            // Delegate further configuration to service upon init.
            $extensionManagerDefinition->addMethodCall('autoconfigure');
        }

        return $this;
    }
}
