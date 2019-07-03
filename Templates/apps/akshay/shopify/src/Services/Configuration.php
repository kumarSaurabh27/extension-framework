<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\CoreFrameworkBundle\Framework\ExtendableComponentManager;

class Configuration
{
    public function __construct(ContainerInterface $container, ExtendableComponentManager $extendableComponentManager)
	{
        $this->container = $container;
        $this->extendableComponentManager = $extendableComponentManager;
    }

    
}
