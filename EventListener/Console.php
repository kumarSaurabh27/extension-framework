<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\EventListener;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Command as SymfonyFrameworkCommand;
use Webkul\UVDesk\ExtensionFrameworkBundle\Extensions\PackageManager;

class Console
{
    private $kernel;
    private $container;

    public function __construct(ContainerInterface $container, KernelInterface $kernel, PackageManager $packageManager)
    {
        $this->kernel = $kernel;
        $this->container = $container;
        $this->packageManager = $packageManager;
    }

    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $command = $event->getCommand();

        switch (true) {
            case $command instanceof SymfonyFrameworkCommand\CacheClearCommand:
                // $application = new Application($this->kernel);

                // $application->setAutoExit(false);
                // $application->run(new ArrayInput(['command' => 'uvdesk_extensions:build']), $event->getOutput());
                break;
            case $command instanceof SymfonyFrameworkCommand\AssetsInstallCommand:
                $assets = [];
                $base = dirname(__DIR__) . '/Resources/public/extensions';

                foreach ($this->packageManager->getPackages() as $package) {
                    $metadata = $package->getMetadata();
                    $extensionReflectionClass = new \ReflectionClass(current(array_keys($metadata->getExtensionReferences())));

                    $source = dirname($extensionReflectionClass->getFileName()) . '/Resources/public';

                    if (file_exists($source) && is_dir($source)) {
                        $assets[] = [
                            'source' => $source,
                            'destination' => [
                                'base' => $base . "/" . $metadata->getVendor(),
                                'path' => $base . "/" . $metadata->getVendor() . "/" . $metadata->getPackage(),
                            ],
                        ];
                    }
                }
                
                // Clear existing resources from extensions directory
                $this->emptyDirectory($base);

                // Link package assets within bundle assets
                foreach ($assets as $asset) {
                    if (!is_dir($asset['destination']['base'])) {
                        mkdir($asset['destination']['base'], 0755, true);
                    }

                    symlink($asset['source'], $asset['destination']['path']);
                }
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

    private function emptyDirectory($path)
    {
        $iterator = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $collection = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($collection as $info) {
            if ($info->isDir()) {
                rmdir($info->getRealPath());
            } else {
                unlink($info->getRealPath());
            }
        }
    }
}
