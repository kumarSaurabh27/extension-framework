<?php

namespace Webkul\UVDesk\ExtensionBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Command as SymfonyFrameworkCommand;

class Console
{
    private $container;
    private $entityManager;

    public function __construct(ContainerInterface $container, EntityManager $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $command = $event->getCommand();

        switch (true) {
            case $command instanceof SymfonyFrameworkCommand\CacheClearCommand:
                $this->compileApplicationPackages($event);
                break;
            default:
                break;
        }

        return;
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        return;
    }

    private function compileApplicationPackages(ConsoleCommandEvent $event)
    {
        $output = $event->getOutput();

        // Compile collection of available applications
        $pathToExtensionLockFile = $this->container->getParameter('uvdesk_extensions.dir') . '/extensions.json';

        if (!file_exists($pathToExtensionLockFile)) {
            $output->writeLn("\n<comment>Missing extensions.json. Helpdesk extensions will be disabled.</comment>");

            return;
        }

        $extensionsDir = $this->container->getParameter('uvdesk_extensions.dir');
        $lockedExtensionConfigurations = json_decode(file_get_contents($pathToExtensionLockFile), true);

        // foreach ($extensions['vendors'] as $vendor) {
        //     foreach ($vendor['extensions'] as $extension) {
        //         $qualifiedExtensionName = $vendor['name'] . "/" . $extension['name'];
        //         $pathToExtensionConfigurationFile = $extensionsDir . '/' . $qualifiedExtensionName . '/extension.json';

        //         if (!file_exists($pathToExtensionConfigurationFile) || is_dir($pathToExtensionConfigurationFile)) {
        //             throw new \Exception("Unable to locate configuration file for extension " . $qualifiedExtensionName . ".");
        //         }

        //         $extensionConfiguration = json_decode(file_get_contents($pathToExtensionConfigurationFile), true);

        //         if ($qualifiedExtensionName != $extensionConfiguration['name']) {
        //             throw new \Exception("Unable to load configuration file for extension " . $qualifiedExtensionName . ". The file was found but no valid configuration were found for extension " . $qualifiedExtensionName . ".");
        //         } else if ($extensionConfiguration['type'] != 'uvdesk-ecommerce-extension') {
        //             throw new \Exception("Unable to load configuration file for extension " . $qualifiedExtensionName . ". Extension type " . $extensionConfiguration['type'] . " is not supported.");
        //         }

        //         dump($extensionConfiguration);
        //         die;
        //     }
        // }

        $pathToProjectComposerJson = $this->container->get('kernel')->getProjectDir() . '/composer.json';
        $composerConfiguration = json_decode(file_get_contents($pathToProjectComposerJson), true);

        $autoloadedNamespaceCollection = $composerConfiguration['autoload']['psr-4'];

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
