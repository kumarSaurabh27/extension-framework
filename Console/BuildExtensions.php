<?php

namespace Webkul\UVDesk\ExtensionBundle\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BuildExtensions extends Command
{
    private $extensions_directory;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('uvdesk_extensions:build');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->extensions_directory = $this->container->getParameter('uvdesk_extensions.dir');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ('dev' != $this->container->get('kernel')->getEnvironment()) {
            $output->writeln("\n<comment>This command is only allowed to be used in development environment.</comment>");

            return;
        } else if (!file_exists($this->extensions_directory . '/extensions.json')) {
            $output->writeln("\n<comment>Missing extensions.json. Helpdesk extensions will be disabled.</comment>");
            
            return;
        }

        $configurations = json_decode(file_get_contents($this->extensions_directory . '/extensions.json'), true);

        dump($configurations);
        die;

        foreach ($configurations['vendors'] as $vendor => $vendorAttr) {
            foreach ($vendorAttr['extensions'] as $extension) {
                $qualifiedExtensionName = $vendor['name'] . "/" . $extension['name'];
                $pathToExtensionConfigurationFile = $pathToExtensionsDirectory . '/' . $qualifiedExtensionName . '/extension.json';

                if (!file_exists($pathToExtensionConfigurationFile) || is_dir($pathToExtensionConfigurationFile)) {
                    throw new \Exception("Unable to locate configuration file for extension " . $qualifiedExtensionName . ".");
                }

                $extensionConfiguration = json_decode(file_get_contents($pathToExtensionConfigurationFile), true);

                if ($qualifiedExtensionName != $extensionConfiguration['name']) {
                    throw new \Exception("Unable to load configuration file for extension " . $qualifiedExtensionName . ". The file was found but no valid configuration were found for extension " . $qualifiedExtensionName . ".");
                } else if ($extensionConfiguration['type'] != 'uvdesk-ecommerce-extension') {
                    throw new \Exception("Unable to load configuration file for extension " . $qualifiedExtensionName . ". Extension type " . $extensionConfiguration['type'] . " is not supported.");
                }

                dump($extensionConfiguration);
                die;
            }
        }

        $pathToProjectComposerJson = $this->container->get('kernel')->getProjectDir() . '/composer.json';
        $composerConfiguration = json_decode(file_get_contents($pathToProjectComposerJson), true);

        $autoloadedNamespaceCollection = $composerConfiguration['autoload']['psr-4'];

        dump($lockedExtensionConfigurations);
        dump($autoloadedNamespaceCollection);
        die;

        // foreach ($lockedExtensionConfigurations['vendors'] as $vendor => $extensions) {
        //     foreach ($extensions)
        //     foreach ($extension['autoload'] as $namespace => $path) {
        //         if (!array_key_exists($namespace, $autoloadedNamespaceCollection)) {
        //             $autoloadedNamespaceCollection[$namespace] = $path;
        //         }
        //     }
        // }

        foreach ($lockedExtensionConfigurations['extensions'] as $extension) {
            foreach ($extension['autoload'] as $namespace => $path) {
                if (!array_key_exists($namespace, $autoloadedNamespaceCollection)) {
                    $autoloadedNamespaceCollection[$namespace] = $path;
                }
            }
        }

        if (array_diff($autoloadedNamespaceCollection, $composerConfiguration['autoload']['psr-4']) != null) {
            $composerConfiguration['autoload']['psr-4'] = $autoloadedNamespaceCollection;
            file_put_contents($pathToProjectComposerJson, json_encode($composerConfiguration, JSON_PRETTY_PRINT));

            $output->writeln("New extensions have been found and added to composer.json. Please run 'composer dump-autoload' to update your composer autloader definitions.");
        }

        // @TODO:
        // - Check if all the vendor directories are autoloaded
        // - Depending on the state, dump composer autoloaders to reflect the new state
    }
}