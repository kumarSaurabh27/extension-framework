<?php

namespace Webkul\UVDesk\ExtensionBundle\Hooks;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ECommerceApplication
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
