<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Package\Package;
use Webkul\UVDesk\ExtensionFrameworkBundle\Package\ExecutablePackage;
use Webkul\UVDesk\ExtensionFrameworkBundle\Package\ExecutablePackageInterface;

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

        $packages = $this->searchPackages();
        $lockfile = $this->updateLockfile($packages);

        $this->updateComposerJson($lockfile, $output);
        $this->autoconfigurePackages($packages, $output);
    }

    private function searchPackages()
    {
        $packages = [];
        $path = $this->container->getParameter('uvdesk_extensions.dir');

        if (!file_exists($path) || !is_dir($path)) {
            throw new \Exception("No apps directory found. Looked in $path");
        }

        foreach (array_diff(scandir($path), ['.', '..']) as $vendorName) {
            $vendorDirectory = "$path/$vendorName";

            if (file_exists($vendorDirectory) && is_dir($vendorDirectory)) {
                foreach (array_diff(scandir($vendorDirectory), ['.', '..']) as $packageName) {
                    $extensionJson = "$vendorDirectory/$packageName/extension.json";
    
                    if (file_exists($extensionJson) && !is_dir($extensionJson)) {
                        $package = new Package($extensionJson);

                        if ($vendorName != $package->getVendor() || $packageName != $package->getPackage()) {
                            throw new \Exception("Invalid package extension.json file. The qualified package name should be '$vendorName/$packageName' but the specified name is '" . $package->getName() . "' in '$extensionJson'");
                        }

                        $packages[] = $package;
                    }
                }
            }
        }

        // Sort packages alphabetically
        usort($packages, function($package_1, $package_2) {
			return strcasecmp($package_1->getName(), $package_2->getName());
        });

        return $packages;
    }

    private function updateLockfile(array $packages = [])
    {
        $lockfile = [
            '_readme' => [
                "This file locks the dependencies of your project to a known state",
                "This file is @generated automatically. Avoid making changes to this file directly.",
            ],
            'packages' => array_map(function ($package) {
                $json = [
                    'name' => $package->getName(),
                    'description' => $package->getDescription(),
                    'autoload' => $package->getDefinedNamespaces(),
                    'extensions' => $package->getExtensionReferences(),
                ];
    
                if (null != $package->getScripts()) {
                    $json['scripts'] = $package->getScripts();
                }

                return $json;
            }, $packages),
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

    private function autoconfigurePackages(array $packages = [], $output)
    {
        $pathToConfig = $this->container->getParameter('kernel.project_dir') . "/config/extensions";

        if (!file_exists($pathToConfig) || !is_dir($pathToConfig)) {
            mkdir($pathToConfig, 0755, true);
        }

        foreach ($packages as $package) {
            $scripts = $package->getScripts();

            foreach ($package->getScripts() as $script) {
                $reflectionClass = null;

                try {
                    $reflectionClass = new \ReflectionClass($script);
                } catch (\ReflectionException $e) {
                    $file = null;
                    $iterations = explode('\\', $script);

                    foreach ($package->getDefinedNamespaces() as $namespace => $relativePath) {
                        $depth = 1;
                        $namespaceIterations = explode('\\', $namespace);

                        foreach ($iterations as $index => $iteration) {
                            if (empty($namespaceIterations[$index]) || $namespaceIterations[$index] != $iteration) {
                                break;
                            }

                            $depth++;
                        }

                        if (0 === (count($namespaceIterations) - $depth)) {
                            $relativePath .= str_ireplace([$namespace, "\\"], ["", "/"], $script);
                            $file = $package->getRootDirectory() . "/$relativePath.php";
                            break;
                        }
                    }
                } finally {
                    if (empty($reflectionClass)) {
                        if (empty($file)) {
                            throw new \Exception("Class $script does not exist");
                        } else {
                            include_once $file;

                            $reflectionClass = new \ReflectionClass($script);

                            if (false == $reflectionClass->implementsInterface(ExecutablePackageInterface::class)) {
                                throw new \Exception("Class $script needs to implement " . ExecutablePackageInterface::class);
                            }
                        }
                    }
                }
                
                $reflectionClass->setStaticPropertyValue('directory', $pathToConfig);
                $reflectionClass->newInstance($package)->install();
            }
        }
    }
}
