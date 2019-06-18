<?php

namespace Webkul\UVDesk\ExtensionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Webkul\UVDesk\ExtensionBundle\Framework\CommunityApplication;
use Webkul\UVDesk\ExtensionBundle\Extensions\CommunityApplicationManager;
use Webkul\UVDesk\ExtensionBundle\Framework\CommunityApplicationInterface;
use Webkul\UVDesk\ExtensionBundle\Framework\CommunityModuleExtensionInterface;

class Builder extends Extension
{
    private $extensions = [];

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
        $container->registerForAutoconfiguration(CommunityModuleExtensionInterface::class)->addTag(CommunityModuleExtensionInterface::class);

        // Compile apps
        $path = $container->getParameter('uvdesk_extensions.dir') . "/extensions.json";
        
        if (file_exists($path)) {
            $this
                ->compileExtensions($path)
                ->autoConfigureExtensions($container, $loader);
        }
    }

    private function compileExtensions($path) : Builder
    {
        $json = json_decode(file_get_contents($path), true);

        foreach ($json['vendors'] as $vendor => $attributes) {
            foreach ($attributes['extensions'] as $extension => $extensionClassPath) {
                $reflection = new \ReflectionClass($extensionClassPath);

                if (!$reflection->implementsInterface(CommunityModuleExtensionInterface::class)) {
                    $message = "Extension %s/%s [%s] is not supported.";

                    throw new \Exception(sprintf($message, $vendor, $extension, $reflection->getName())); 
                }

                $this->extensions[] = [
                    'vendor' => $vendor,
                    'extension' => $extension,
                    'reference' => $reflection,
                ];
            }
        }

        return $this;
    }

    private function autoConfigureExtensions(ContainerBuilder $container, YamlFileLoader $loader) : Builder
    {
        if ($container->has(CommunityApplicationManager::class)) {
            $applicationManagerDefinition = $container->findDefinition(CommunityApplicationManager::class);
        
            foreach ($this->extensions as $attributes) {
                $extension = $attributes['reference']->newInstanceWithoutConstructor();

                // Register extension services
                foreach ($extension::getServices() as $resource) {
                    $loader->load($resource);
                }

                // Register extension provided applications
                foreach ($extension::getApplications() as $application) {
                    // @TODO: Check if class is valid and accessible
                    $reflection = new \ReflectionClass($application);

                    if ($reflection->isSubclassOf(CommunityApplication::class)) {
                        $applicationManagerDefinition->addMethodCall('registerApplication', array(new Reference($application), $attributes['vendor'], $attributes['extension']));
                    }
                }
            }
        }

        return $this;
    }
}
