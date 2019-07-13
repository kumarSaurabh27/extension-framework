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
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package\Package;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Module\ClassMap;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ModuleInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\RoutingResourceInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ConfigurablePackageInterface;

class ContainerExtension extends Extension
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

        // Process uvdesk lock file
        $path = $container->getParameter("kernel.project_dir") . "/uvdesk.lock";

        if (file_exists($path) && $container->has(Package::class) && $container->has(ClassMap::class)) {
            $package = $container->findDefinition(Package::class);
            $classMap = $container->findDefinition(ClassMap::class);

            // $container->registerForAutoconfiguration(RoutingResourceInterface::class)->addTag(RoutingResourceInterface::class);

            $env = $container->getParameter('kernel.environment');
            $uvdesk = json_decode(file_get_contents($path), true);
            $pathToConfigs = $container->getParameter("kernel.project_dir") . "/config/extensions";

            $extensionConfigurations = $this->parseExtensionConfigurations($pathToConfigs);

            // Prepare packages for configuration
            foreach ($uvdesk['packages'] as $metadata) {
                $reference = current(array_keys($metadata['extensions']));
                $supportedEnvironments = $metadata['extensions'][$reference];

                // Check if extension is supported in the current environment
                if (in_array('all', $supportedEnvironments) || in_array($env, $supportedEnvironments)) {
                    $class = new \ReflectionClass($reference);
                    
                    if (!$class->implementsInterface(ModuleInterface::class)) {
                        throw new \Exception("Class $reference could not be registered as a module. Please check that it implements the " . ModuleInterface::class . " interface.");
                    }
                    
                    $module = $class->newInstanceWithoutConstructor();

                    dump($module);

                    // // Load extension services
                    // foreach ((array) $extension->getServices() as $resource) {
                    //     $loader->load($resource);
                    // }
                    
                    // Prepare extension for configuration
                    $this->prepareModule($container, $package, $module, $metadata, $extensionConfigurations);
                }
            }

            die;
    
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

    private function prepareModule(ContainerBuilder $container, Definition $package, ModuleInterface $module, array $metadata = [], array $configurations = [])
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
            $qualifiedName = str_replace('/', '_', $metadata['name']);

            if (empty($configurations[$qualifiedName])) {
                throw new \Exception("No available configurations found for package '" . $metadata['name'] . "'");
            }

            $params = $this->processConfiguration($moduleConfiguration, $configurations[$qualifiedName]);
        }

        $root = $container->getParameter("uvdesk_extensions.dir") . "/" . $metadata['name'];
        $package->addMethodCall('configurePackage', array($root, $metadata, $params, new Reference($module->getPackageReference())));

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

            $package->addMethodCall('configureApplication', array(new Reference($reference), new Reference($module->getPackageReference())));
        }
    }
}
