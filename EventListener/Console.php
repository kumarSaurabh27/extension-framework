<?php

namespace Webkul\UVDesk\ExtensionBundle\EventListener;

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
                // $application = new Application($this->kernel);
                
                // $application->setAutoExit(false);
                // $application->run(new ArrayInput(['command' => 'uvdesk_extensions:build']), $event->getOutput());
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
