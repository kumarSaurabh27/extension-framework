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

use Webkul\UVDesk\ExtensionFrameworkBundle\Configurators\AppConfigurator;
use Webkul\UVDesk\ExtensionFrameworkBundle\Configurators\PackageConfigurator;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package\PackageInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application\ApplicationInterface;

// use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\RoutingResourceInterface;
// use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ConfigurablePackageInterface;

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
        // Define parameters
        foreach ($this->processConfiguration($this->getConfiguration($configs, $container), $configs) as $param => $value) {
            switch ($param) {
                case 'dir':
                    $container->setParameter("uvdesk_extensions.dir", $value);
                    break;
                default:
                    break;
            }
        }

        // Define services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        // Compile modules
        $env = $container->getParameter('kernel.environment');
        $path = $container->getParameter("kernel.project_dir") . "/uvdesk.lock";

        foreach ($this->getCachedPackages($path) as $attributes) {
            $reference = current(array_keys($attributes['package']));
            $supportedEnvironments = $attributes['package'][$reference];

            // Check if package is supported in the current environment
            if (in_array('all', $supportedEnvironments) || in_array($env, $supportedEnvironments)) {
                $class = new \ReflectionClass($reference);
                
                if (!$class->implementsInterface(PackageInterface::class)) {
                    throw new \Exception("Class $reference could not be registered as a package. Please check that it implements the " . PackageInterface::class . " interface.");
                }

                // Prepare package for configuration
                // https://symfony.com/doc/current/service_container/configurators.html
                $this->preparePackage($container, $loader, $class, $attributes);
            }
        }

        // Configure services
        $container->registerForAutoconfiguration(PackageInterface::class)->addTag(PackageInterface::class);
        $container->registerForAutoconfiguration(ApplicationInterface::class)->addTag(ApplicationInterface::class);
        $container->registerForAutoconfiguration(PackageInterface::class)->setConfigurator([PackageConfigurator::class, 'configure']);
        $container->registerForAutoconfiguration(ApplicationInterface::class)->setConfigurator([ApplicationConfigurator::class, 'configure']);
    }

    private function getCachedPackages($path) : array
    {
        try {
            if (file_exists($path)) {
                return json_decode(file_get_contents($path), true)['packages'] ?? [];
            }
        } catch (\Exception $e) {
            // Skip module compilation ...
            return [];
        }
    }

    private function parseConfigurations()
    {
        // $pathToConfigs = $container->getParameter("kernel.project_dir") . "/config/extensions";

        // $extensionConfigurations = $this->parseExtensionConfigurations($pathToConfigs);
        return null;
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

    private function preparePackage(ContainerBuilder $container, YamlFileLoader $services, \ReflectionClass $reflection, array $attributes)
    {
        $package = $reflection->newInstance();

        // Process package configuration
        // $configuration = $package->getConfiguration();

        // if (!empty($configuration)) {
        //     $qualifiedName = str_replace('/', '_', $metadata['name']);

        //     if (empty($configurations[$qualifiedName])) {
        //         throw new \Exception("No available configurations found for package '" . $metadata['name'] . "'");
        //     }

        //     $params = $this->processConfiguration($moduleConfiguration, $configurations[$qualifiedName]);
        // }

        // Load package services
        $path = dirname($reflection->getFileName()) . "/Resources/config/services.yaml";

        if (file_exists($path)) {
            $services->load($path);
        }

        // Configure package
        // $package = $module->getPackage();
        // @TODO: Configure packages using configurator

        // $root = $container->getParameter("uvdesk_extensions.dir") . "/" . $metadata['name'];
        // $packageManager->addMethodCall('configurePackage', array($root, $metadata, $params, new Reference($module->getPackageReference())));

        // // Register available applications with the extension manager for auto init.
        // foreach ($module->getApplicationReferences() as $reference) {
        //     $class = new \ReflectionClass($reference);

        //     if (!$class->implementsInterface(ApplicationInterface::class) || !$class->implementsInterface(EventSubscriberInterface::class)) {
        //         throw new \Exception("Class $reference could not be registered as an application. Please check that it implements both the " . ApplicationInterface::class . " and " . EventSubscriberInterface::class . " interfaces.");
        //     }

        //     // Override application definition
        //     $applicationDefinition = $container->findDefinition($reference);
        //     $applicationDefinition
        //         ->setPrivate(true)
        //         ->setAutowired(true)
        //         ->setAutoconfigured(false);

        //     $packageManager->addMethodCall('configureApplication', array(new Reference($reference), new Reference($module->getPackageReference())));
        // }
    }
}
