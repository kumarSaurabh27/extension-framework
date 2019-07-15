<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\EventListener;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Command as SymfonyFrameworkCommand;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\PackageManager;

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
                if (file_exists($base) && is_dir($base)) {
                    $this->emptyDirectory($base);
                }

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

    private function scanDirectory(string $path, bool $return_full_path = true)
    {
        if (!file_exists($path) || !is_dir($path)) {
            throw new \Exception("Not a directory : '$path'");
        }

        $scannedFiles = array_diff(scandir($path), ['.', '..']);

        if ($return_full_path) {
            $scannedFiles = array_map(function ($file) use ($path) {
                return "$path/$file";
            }, $scannedFiles);    
        }

        return $scannedFiles;
    }

    private function emptyDirectory(string $path)
    {
        if (!file_exists($path) || !is_dir($path)) {
            throw new \Exception("Not a directory : '$path'");
        }
        
        $scannedFiles = $this->scanDirectory($path);

        if (!empty($scannedFiles)) {
            foreach ($scannedFiles as $filepath) {
                if (!is_dir($filepath) || is_link($filepath)) {
                    unlink($filepath);
                } else {
                    if (null != $this->scanDirectory($filepath)) {
                        $this->emptyDirectory($filepath);
                    }

                    rmdir($filepath);
                }
            }
        }
    }
}
