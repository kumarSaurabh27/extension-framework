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
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\MappingResource;
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
        $mappingResource = $container->findDefinition(MappingResource::class);
        $packageConfigurations = $this->parsePackageConfigurations($container->getParameter("kernel.project_dir") . "/config/extensions");

        foreach ($this->getCachedPackages($path) as $attributes) {
            $reference = current(array_keys($attributes['package']));
            $supportedEnvironments = $attributes['package'][$reference];

            // Check if package is supported in the current environment
            if (in_array('all', $supportedEnvironments) || in_array($env, $supportedEnvironments)) {
                $class = new \ReflectionClass($reference);
                
                if (!$class->implementsInterface(PackageInterface::class)) {
                    throw new \Exception("Class $reference could not be registered as a package. Please check that it implements the " . PackageInterface::class . " interface.");
                }

                // @TODO: Check package configurations
                $configuration = $class->newInstanceWithoutConstructor()->getConfiguration();

                if (!empty($configuration)) {
                    $qualifiedName = str_replace('/', '_', $attributes['name']);

                    dump($configuration);
                    dump($qualifiedName);
                    die;

                    // if (empty($availableConfigurations[$qualifiedName])) {
                    //     throw new \Exception("No available configurations found for package '" . $attributes['name'] . "'");
                    // }

                    // $params = $this->processConfiguration($moduleConfiguration, $availableConfigurations[$qualifiedName]);
                }

                // Prepare package for configuration
                $this->loadPackageServices($class->getFileName(), $loader);

                if ($container->hasDefinition($reference)) {
                    $mappingResource->addMethodCall('setMetadata', array($reference, $attributes));
                }
            }
        }

        // Configure services
        $container->registerForAutoconfiguration(PackageInterface::class)->addTag(PackageInterface::class)->setLazy(true)->setPublic(true);
        $container->registerForAutoconfiguration(ApplicationInterface::class)->addTag(ApplicationInterface::class)->setLazy(true)->setPublic(true);
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

    private function loadPackageServices($classPath, YamlFileLoader $loader)
    {
        $path = dirname($classPath) . "/Resources/config/services.yaml";

        if (file_exists($path)) {
            $loader->load($path);
        }
    }

    private function parsePackageConfigurations($prefix) : array
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
}
