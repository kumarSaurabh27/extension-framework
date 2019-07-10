<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\PackageMetadata;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ModuleInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ExecutablePackage;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ExecutablePackageInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ConfigurablePackageInterface;

class BuildExtensions extends Command
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('uvdesk:extensions:build');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ('dev' != $this->container->get('kernel')->getEnvironment()) {
            $output->writeln("\n<comment>This command is only allowed to be used in development environment.</comment>");

            return;
        }

        $metadata = $this->prepareMetadata();
        $lockfile = $this->updateLockfile($metadata);

        $this->updateComposerJson($lockfile, $output);
        $this->autoconfigurePackages($metadata, $output);
    }

    private function prepareMetadata()
    {
        $metadata = [];
        $path = $this->container->getParameter('uvdesk_extensions.dir');

        if (!file_exists($path) || !is_dir($path)) {
            throw new \Exception("No apps directory found. Looked in $path");
        }

        foreach (array_diff(scandir($path), ['.', '..']) as $vendor) {
            $directory = "$path/$vendor";

            if (file_exists($directory) && is_dir($directory)) {
                foreach (array_diff(scandir($directory), ['.', '..']) as $package) {
                    $root = "$directory/$package";
    
                    if (file_exists($root) && is_dir($root)) {
                        $packageMetadata = new PackageMetadata($root);

                        if ($vendor != $packageMetadata->getVendor() || $package != $packageMetadata->getPackage()) {
                            throw new \Exception("Invalid package extension.json file. The qualified package name should be '$vendor/$package' but the specified name is '" . $packageMetadata->getName() . "'");
                        }

                        $metadata[] = $packageMetadata;
                    }
                }
            }
        }

        // Sort packages alphabetically
        usort($metadata, function($data_a, $data_b) {
			return strcasecmp($data_a->getName(), $data_b->getName());
        });

        return $metadata;
    }

    private function updateLockfile(array $metadata = [])
    {
        $lockfile = [
            '_readme' => [
                "This file locks the dependencies of your project to a known state",
                "This file is @generated automatically. Avoid making changes to this file directly.",
            ],
            'packages' => array_map(function ($packageMetadata) {
                return [
                    'name' => $packageMetadata->getName(),
                    'description' => $packageMetadata->getDescription(),
                    'type' => $packageMetadata->getType(),
                    'autoload' => $packageMetadata->getDefinedNamespaces(),
                    'extensions' => $packageMetadata->getExtensionReferences(),
                ];;
            }, $metadata),
        ];

        $path = $this->container->getParameter('kernel.project_dir') . "/uvdesk.lock";
        file_put_contents($path, json_encode($lockfile, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $lockfile;
    }

    private function updateComposerJson(array $lockfile = [], $output)
    {
        $path = $this->container->getParameter('kernel.project_dir') . "/composer.json";
        $prefix = str_ireplace($this->container->getParameter('kernel.project_dir') . "/", "", $this->container->getParameter('uvdesk_extensions.dir'));
        
        $json = json_decode(file_get_contents($path), true);
        $psr4_current = $psr4_modified = $json['autoload']['psr-4'] ?? [];

        foreach ($lockfile['packages'] as $package) {
            foreach ($package['autoload'] as $namespace => $relativePath) {
                $psr4_modified[$namespace] = "$prefix/" . $package['name'] . "/" . $relativePath;
            }
        }

        if (array_diff($psr4_modified, $psr4_current) != null) {
            $json['autoload']['psr-4'] = $psr4_modified;
            file_put_contents($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            $output->writeln("New extensions have been found and added to composer.json. Please run 'composer dump-autoload' to update your composer autloading schematic.");
        }
    }

    private function getUnloadedReflectionClass(string $class, PackageMetadata $metadata) : \ReflectionClass
    {
        try {
            $reflectionClass = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            $classPath = null;
            $iterations = explode('\\', $class);

            foreach ($metadata->getDefinedNamespaces() as $namespace => $path) {
                $depth = 1;
                $namespaceIterations = explode('\\', $namespace);

                foreach ($iterations as $index => $iteration) {
                    if (empty($namespaceIterations[$index]) || $namespaceIterations[$index] != $iteration) {
                        break;
                    }

                    $depth++;
                }

                if (0 === (count($namespaceIterations) - $depth)) {
                    $path .= str_ireplace([$namespace, "\\"], ["", "/"], $class);
                    $classPath = $metadata->getRoot() . "/$path.php";
                    break;
                }
            }
        } finally {
            if (empty($reflectionClass) && !empty($classPath)) {
                include_once $classPath;

                $reflectionClass = new \ReflectionClass($class);
            } else if (empty($reflectionClass)) {
                throw new \Exception("Class $class does not exist");
            }
        }

        return $reflectionClass;
    }

    private function autoconfigurePackages(array $metadata = [], $output)
    {
        $pathToConfig = $this->container->getParameter('kernel.project_dir') . "/config/extensions";

        if (!file_exists($pathToConfig) || !is_dir($pathToConfig)) {
            mkdir($pathToConfig, 0755, true);
        }

        foreach ($metadata as $packageMetadata) {
            $class = current(array_keys($packageMetadata->getExtensionReferences()));
            $reflectionClass = $this->getUnloadedReflectionClass($class, $packageMetadata);
            
            if (!$reflectionClass->implementsInterface(ModuleInterface::class)) {
                throw new \Exception("Class $class could not be registered as an extension. Please check that it implements the " . ModuleInterface::class . " interface.");
            }

            $extension = $reflectionClass->newInstance();
            $packageReflectionClass = $this->getUnloadedReflectionClass($extension->getPackageReference(), $packageMetadata);

            if ($packageReflectionClass->implementsInterface(ConfigurablePackageInterface::class)) {
                $configurablePackage = $packageReflectionClass->newInstanceWithoutConstructor();
                $configurablePackage->setPathToConfigurationFile($pathToConfig . "/" . str_replace('/', '_', $packageMetadata->getName()) . ".yaml");
                $configurablePackage->install();
            }
        }
    }
}
