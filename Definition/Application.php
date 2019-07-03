<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class Application implements ApplicationInterface, EventSubscriberInterface
{
    public static abstract function getMetadata() : ApplicationMetadata;

    public static abstract function getSubscribedEvents();

    final public function setPackage(PackageInterface $package) : ApplicationInterface
	{
        $this->package = $package;

        return $this;
    }
    
    final public function getPackage() : PackageInterface
    {
        return $this->package;
    }
}
