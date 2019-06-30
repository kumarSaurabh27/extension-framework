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

class BuildExtensions extends Command
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('uvdesk:apps:update-lock');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ('dev' != $this->container->get('kernel')->getEnvironment()) {
            $output->writeln("\n<comment>This command is only allowed to be used in development environment.</comment>");

            return;
        }

        $root = $this->container->get('kernel')->getProjectDir();

        // Parse vendor directories and update lock file
        $uvdesk = $this->updateExtensionLockFile("$root/uvdesk.lock");

        if (!empty($uvdesk['packages'])) {
            $prefix = str_ireplace("$root/", "", $this->container->getParameter('uvdesk_extensions.dir'));

            // Check autoloader state
            $composer = json_decode(file_get_contents("$root/composer.json"), true);
            $extensionAutoloadedNamespaceCollection = $autoloadedNamespaceCollection = !empty($composer['autoload']['psr-4']) ? $composer['autoload']['psr-4'] : [];

            foreach ($uvdesk['packages'] as $package) {
                foreach ($package['autoload'] as $namespace => $relativePath) {
                    $extensionAutoloadedNamespaceCollection[$namespace] = "$prefix/" . $package['name'] . "/" . $relativePath;
                }
            }

            if (array_diff($extensionAutoloadedNamespaceCollection, $autoloadedNamespaceCollection) != null) {
                $composer['autoload']['psr-4'] = $extensionAutoloadedNamespaceCollection;
                file_put_contents("$root/composer.json", json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                
                $output->writeln("New extensions have been found and added to composer.json. Please run 'composer dump-autoload' to update your composer autloading schematic.");
            }
        }
    }

    private function updateExtensionLockFile()
    {
        $packages = [];
        $extensionsDirectory = $this->container->getParameter('uvdesk_extensions.dir');

        if (!file_exists($extensionsDirectory) || !is_dir($extensionsDirectory)) {
            throw new \Exception("No apps directory found. Looked in $extensionsDirectory");
        }

        foreach (array_diff(scandir($extensionsDirectory), ['.', '..']) as $vendor) {
            $vendorDirectory = $extensionsDirectory . "/" . $vendor;

            // Only proceed if path is a non-empty directory
            if (!file_exists($vendorDirectory) || !is_dir($vendorDirectory)) {
                continue;
            }
            
            $directories = array_diff(scandir($vendorDirectory), ['.', '..']);
            $vendorPackages = array_filter($directories, function ($directory) use ($vendorDirectory) {
                $path = "$vendorDirectory/$directory";
                $extensionJson = "$path/extension.json";

                return (file_exists($path) && is_dir($path) && file_exists($extensionJson) && !is_dir($extensionJson));
            });

            foreach ($vendorPackages as $package) {
                $package = Package::createFromAttributes($vendor, $package, "$vendorDirectory/$package/extension.json");

                if ($package->isValid() && null != $package->getExtension()) {
                    $packages[] = $package;
                }
            }
        }

        // Sort packages alphabetically
        usort($packages, function($package_1, $package_2) {
			return strcasecmp($package_1->getName(), $package_2->getName());
        });
        
        // Prepare dataset for lock file
        $json['packages'] = array_map(function ($package) {
            return [
                'name' => $package->getName(),
                'description' => $package->getDescription(),
                'type' => $package->getType(),
                "extension" => $package->getExtension()->getName(),
                'autoload' => $package->getDefinedNamespaces(),
                'suggest' => [
                    'uvdesk/ecommerce' => "Integrate orders sync. to tickets"
                ],
            ];
        }, $packages);

        file_put_contents($this->container->get('kernel')->getProjectDir() . "/uvdesk.lock", json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $json;
    }
}
