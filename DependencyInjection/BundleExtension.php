<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\DependencyInjection;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Webkul\UVDesk\ExtensionFrameworkBundle\Extensions\PackageManager;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ModuleInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\RoutineInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationInterface;

class BundleExtension extends Extension
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
        // Handle bundle configurations
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

        // Load Services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        // Configure services
        $container->registerForAutoconfiguration(RoutineInterface::class)->addTag(RoutineInterface::class);

        // Process uvdesk lock file
        $path = $container->getParameter("kernel.project_dir") . "/uvdesk.lock";

        if (file_exists($path) && $container->has(PackageManager::class)) {
            $env = $container->getParameter('kernel.environment');
            $uvdesk = json_decode(file_get_contents($path), true);
            $packageManagerDefinition = $container->findDefinition(PackageManager::class);
            $extensionConfigurations = $this->parseExtensionConfigurations($container->getParameter("kernel.project_dir") . "/config/extensions");

            // Prepare packages for configuration
            foreach ($uvdesk['packages'] as $package) {
                $reference = current(array_keys($package['extensions']));
                $supportedEnvironments = $package['extensions'][$reference];

                // Check if extension is supported in the current environment
                if (in_array('all', $supportedEnvironments) || in_array($env, $supportedEnvironments)) {
                    $class = new \ReflectionClass($reference);
                    
                    if (!$class->implementsInterface(ModuleInterface::class)) {
                        throw new \Exception("Class $reference could not be registered as an extension. Please check that it implements the " . ModuleInterface::class . " interface.");
                    }

                    $extension = $class->newInstanceWithoutConstructor();

                    // Load extension services
                    foreach ((array) $extension->getServices() as $resource) {
                        $loader->load($resource);
                    }
                    
                    // Prepare extension for configuration
                    $this->prepareExtension($extension, $container, $packageManagerDefinition, $package, $extensionConfigurations);
                }
            }
    
            // Delegate further configuration to service upon init.
            $packageManagerDefinition->addMethodCall('autoconfigure');
        }
    }

    private function parseExtensionConfigurations($prefix) : array
    {
        $configs = [];

        if (file_exists($prefix) && is_dir($prefix)) {
            foreach (array_diff(scandir($prefix), ['.', '..']) as $extensionConfig) {
                $path = "$prefix/$extensionConfig";
    
                if (!is_dir($path) && 'yaml' === pathinfo($path, PATHINFO_EXTENSION)) {
                    $configs[pathinfo($path, PATHINFO_FILENAME)] = Yaml::parseFile($path);
                }
            }
        }

        return $configs;
    }

    private function prepareExtension(ModuleInterface $module, ContainerBuilder $container, Definition $packageManagerDefinition, array $package = [], array $availableConfigurations = [])
    {
        // Override module definition
        $moduleDefinition = $container->findDefinition(get_class($module));
        $moduleDefinition
            ->setPrivate(true)
            ->setAutowired(false)
            ->setAutoconfigured(false);
        
        // Override package definition
        $modulePackageDefinition = $container->findDefinition($module->getPackageReference());
        $modulePackageDefinition
            ->setPrivate(true)
            ->setAutowired(true)
            ->setAutoconfigured(false);
        
        $params = [];
        $moduleConfiguration = $module->getConfiguration();

        if (!empty($moduleConfiguration)) {
            $qualifiedName = str_replace('/', '_', $package['name']);

            if (empty($availableConfigurations[$qualifiedName])) {
                throw new \Exception("No available configurations found for package '" . $package['name'] . "'");
            }

            $params = $this->processConfiguration($moduleConfiguration, $availableConfigurations[$qualifiedName]);
        }

        $root = $container->getParameter("uvdesk_extensions.dir") . "/" . $package['name'];
        $packageManagerDefinition->addMethodCall('configurePackage', array($root, $package, $params, new Reference($module->getPackageReference())));

        // Register available applications with the extension manager for auto init.
        foreach ($module->getApplicationReferences() as $reference) {
            $class = new \ReflectionClass($reference);

            if (!$class->implementsInterface(ApplicationInterface::class) || !$class->implementsInterface(EventSubscriberInterface::class)) {
                throw new \Exception("Class $reference could not be registered as an application. Please check that it implements both the " . ApplicationInterface::class . " and " . EventSubscriberInterface::class . " interfaces.");
            }

            // Override application definition
            $applicationDefinition = $container->findDefinition($reference);
            $applicationDefinition
                ->setPrivate(true)
                ->setAutowired(true)
                ->setAutoconfigured(false);

            $packageManagerDefinition->addMethodCall('configureApplication', array(new Reference($reference), new Reference($module->getPackageReference())));
        }
    }
}
