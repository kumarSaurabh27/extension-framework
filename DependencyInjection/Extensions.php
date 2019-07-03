<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\DependencyInjection;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\ExtensionManager;
use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\Application;
use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\ApplicationInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Module\ModuleInterface;

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
        $this->autoConfigureExtensions($container, $loader);
    }

    private function readExtensionConfigurations($prefix) : array
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

    private function autoConfigureExtensions(ContainerBuilder $container, YamlFileLoader $loader) : Extensions
    {
        $lockfile = $container->getParameter("kernel.project_dir") . "/uvdesk.lock";
        
        if (!file_exists($lockfile) || !$container->has(ExtensionManager::class)) {
            return $this;
        }

        $this->extensionManagerDefinition = $container->findDefinition(ExtensionManager::class);

        $env = $container->getParameter('kernel.environment');
        $extensionsDirectory = $container->getParameter("uvdesk_extensions.dir");

        $lockfile = json_decode(file_get_contents($lockfile), true);
        
        $configs = $this->readExtensionConfigurations($container->getParameter("kernel.project_dir") . "/config/extensions");

        foreach ($lockfile['packages'] as $attributes) {
            $extensionReference = current(array_keys($attributes['extensions']));
            $supportedEnvironments = $attributes['extensions'][$extensionReference];

            if (!in_array($env, $supportedEnvironments) && !in_array('all', $supportedEnvironments)) {
                // Extension is not supported in current environment
                continue;
            }

            $extensionReflection = new \ReflectionClass($extensionReference);
            $extension = $extensionReflection->newInstanceWithoutConstructor();

            $this->prepareExtension($extension, $loader, $container);
        }

        // Delegate further configuration to service upon init.
        $this->extensionManagerDefinition->addMethodCall('autoconfigure');

        return $this;
    }

    private function prepareExtension(ModuleInterface $extension, ContainerBuilder $container, YamlFileLoader $services)
    {
        foreach ((array) $extension->getServices() as $resource) {
            $services->load($resource);
        }
        
        $extensionDefinition = $container->findDefinition(get_class($extension));
        $extensionDefinition
            ->setPrivate(true)
            ->setAutowired(true)
            ->setAutoconfigured(false);
            
        $extensionPackageDefinition = $container->findDefinition($extension->getPackageReference());
        $extensionPackageDefinition
            ->setPrivate(true)
            ->setAutowired(false)
            ->setAutoconfigured(false);
        
        $params = [];
        $this->extensionManagerDefinition->addMethodCall('registerModule', array(new Reference(get_class($extension))));
        $this->extensionManagerDefinition->addMethodCall('registerPackage', array(new Reference($extension->getPackageReference()), $params));

        $configuration = $extension->getConfiguration();
        dump($extensionDefinition);
        dump($extensionPackageDefinition);
        die;

        // if (!empty($configuration)) {
        //     $filename = str_replace('/', '_', $attributes['name']);

        //     if (empty($configs[$filename])) {
        //         throw new \Exception('Unable to parse config.');
        //     }

        //     // @TODO: Check if we can access the root node
        //     // dump($extensionConfig->getConfigTreeBuilder()->getRootNode());

        //     $params = $this->processConfiguration($configuration, $configs[$filename]);
        // }

            
        $this->extensionManagerDefinition->addMethodCall('registerExtension', array(new Reference($extension->getName())));

        // Register available applications with the extension manager for auto init.
        foreach ($extension->getMethod('getApplications')->invoke(null) as $application) {
            $reflectedApplication = new \ReflectionClass($application);

            if ($reflectedApplication->isSubclassOf(Application::class)) {
                $applicationDefinition = $container->findDefinition($application);
                $applicationDefinition
                    ->setPrivate(true)
                    ->addMethodCall('setExtensionReference', [$extension->getName()]);

                $this->extensionManagerDefinition->addMethodCall('registerApplication', array(new Reference($application)));
            }
        }
    }
}
