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
                $prefix = dirname(__DIR__) . '/Resources/public/extensions';
                $public_directory = $this->container->getParameter("uvdesk_extensions.dir");

                $collection = [];
                foreach ($this->packageManager->getExtensionResources() as $info) {
                    $collection[$prefix . "/" . $info['package']] = $info['path'];
                }

                foreach ($collection as $symlink => $original_path) {
                    if (!is_dir($original_path)) {
                        continue;
                    }

                    $path = substr($symlink, 0, strrpos($symlink, '/'));

                    if (!is_dir($path)) {
                        mkdir($path, 0755, true);
                        symlink($original_path, $symlink);
                    } else if (is_dir($symlink)) {
                        // Remove directory
                        symlink($original_path, $symlink);
                    }
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
}
