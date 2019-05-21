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

        // @TODO:
        // - Check if all the vendor directories are autoloaded
        // - Depending on the state, dump composer autoloaders to reflect the new state
    }
}
