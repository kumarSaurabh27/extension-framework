<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\EventListener;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Command as SymfonyFrameworkCommand;

class Console
{
    private $kernel;
    private $container;

    public function __construct(ContainerInterface $container, KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->container = $container;
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
                // uvdesk apps root directory
                $uvdeskAppsRootDirectory = $this->container->get('kernel')->getProjectDir() . '/apps/uvdesk/';

                // get all apps installed
                $uvdeskAppsCollection = scandir($uvdeskAppsRootDirectory);
                $validUVDeskAppsCollection = array_diff($uvdeskAppsCollection, ['.', '..']);

                // get all the assets of uvdesk apps
                foreach ($validUVDeskAppsCollection as $uvdeskApp) {
                    // create Symbolic link if public directory exists
                    $appAssetsPath = $uvdeskAppsRootDirectory . $uvdeskApp . '/Resources/public';

                    if (is_dir($appAssetsPath)) {
                        $uvdeskAppsExtensionDirectory = $this->container->get('kernel')->getProjectDir() . '/vendor/uvdesk/extensions/Resources/public/extensions/uvdesk/' . $uvdeskApp;
                        symlink($appAssetsPath, $uvdeskAppsExtensionDirectory);
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
