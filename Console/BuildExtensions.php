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
    private $src_ext;
    private $src_dir;
    private $src_composer;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('uvdesk:apps:update-lock');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->src_dir = $this->container->getParameter('uvdesk_extensions.dir');
        $this->src_ext = $this->src_dir . '/extensions.json';
        $this->src_uvdesk_lock = $this->container->get('kernel')->getProjectDir() . '/uvdesk.lock';
        $this->src_composer = $this->container->get('kernel')->getProjectDir() . '/composer.json';

        // // Check if extension.json exists
        // if (!file_exists($this->src_ext) || is_dir($this->src_ext)) {
        //     throw new \Exception("Unable to locate extensions.json (Looked in at " . $this->src_ext . "). Helpdesk extensions will be disabled.");
        // }

        // // Check if composer.json exists
        // if (!file_exists($this->src_composer) || is_dir($this->src_composer)) {
        //     throw new \Exception("Unable to locate composer.json (Looked in at " . $this->src_composer . ").");
        // }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ('dev' != $this->container->get('kernel')->getEnvironment()) {
            $output->writeln("\n<comment>This command is only allowed to be used in development environment.</comment>");

            return;
        }

        // Parse vendor directories and update lock file
        $this->updateExtensionLockFile();
        $packages = json_decode(file_get_contents($this->src_uvdesk_lock), true);

        dump($packages);
        die;

        // Check autoloader state
        $composerJson = json_decode(file_get_contents($this->src_uvdesk), true);
        $autoloadedNamespaceCollection = !empty($composerJson['autoload']['psr-4']) ? $composerJson['autoload']['psr-4'] : [];

        $lockedExtensions = json_decode(file_get_contents($this->src_ext), true);
        $extensionAutoloadedNamespaceCollection = $autoloadedNamespaceCollection;

        foreach ($lockedExtensions['vendors'] as $vendor => $vendor_attributes) {
            foreach ($vendor_attributes['extensions'] as $vendor_extension => $extension_attributes) {
                $extension_conf = $this->src_dir . "/" . $vendor . "/" . $vendor_extension . "/extension.json";

                if (!file_exists($extension_conf) || is_dir($extension_conf)) {
                    throw new \Exception("Unable to locate configuration file for extension " . $vendor . "/" . $vendor_extension  . " (Looked in at " . $extension_conf . ").");
                }

                $configuration = json_decode(file_get_contents($extension_conf), true);

                foreach ($configuration['autoload']['psr-4'] as $extensionNamespace => $pathToNamespace) {
                    $path = str_ireplace($this->container->get('kernel')->getProjectDir() . "/", '', $this->src_dir);
                    $extensionAutoloadedNamespaceCollection[$extensionNamespace] = $path . "/" . $vendor . "/" . $vendor_extension . "/" . $pathToNamespace;
                }
            }
        }

        if (array_diff($extensionAutoloadedNamespaceCollection, $autoloadedNamespaceCollection) != null) {
            $composerJson['autoload']['psr-4'] = $extensionAutoloadedNamespaceCollection;
            file_put_contents($this->src_composer, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            $output->writeln("New extensions have been found and added to composer.json. Please run 'composer dump-autoload' to update your composer autloading schematic.");
        }

        // @TODO:
        // - Check if all the vendor directories are autoloaded
        // - Depending on the state, dump composer autoloaders to reflect the new state
        // - Use reflection class to validate extension type
    }

    private function update()
    {
        // $configurations = json_decode(file_get_contents($this->src_ext), true);

        // foreach ($configurations['vendors'] as $vendor => $vendor_attributes) {
        //     foreach ($vendor_attributes['extensions'] as $vendor_extension => $extension_attributes) {
        //         $qualifiedExtensionName = $vendor . "/" . $vendor_extension;
        //         $pathToExtensionConfigurationFile = $this->src_ext . "/" . $vendor . "/" . $vendor_extension . "/" . $extension_attributes['conf'];

        //         if (!file_exists($pathToExtensionConfigurationFile) || is_dir($pathToExtensionConfigurationFile)) {
        //             throw new \Exception("Unable to locate configuration file for extension " . $qualifiedExtensionName . ".");
        //         }

        //         $extensionConfiguration = json_decode(file_get_contents($pathToExtensionConfigurationFile), true);

        //         if ($qualifiedExtensionName != $extensionConfiguration['name']) {
        //             throw new \Exception("Unable to load configuration file for extension " . $qualifiedExtensionName . ". The file was found but no valid configuration were found for extension " . $qualifiedExtensionName . ".");
        //         } else if ($extensionConfiguration['type'] != 'uvdesk-ecommerce-extension') {
        //             throw new \Exception("Unable to load configuration file for extension " . $qualifiedExtensionName . ". Extension type " . $extensionConfiguration['type'] . " is not supported.");
        //         }
        //     }
        // }
    }

    public function updateExtensionLockFile()
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
                'autoload' => $package->getDefinedNamespaces(),
                'suggest' => [
                    'uvdesk/ecommerce' => "Integrate orders sync. to tickets"
                ],
            ];
        }, $packages);

        file_put_contents($this->src_uvdesk_lock, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
