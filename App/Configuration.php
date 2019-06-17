<?php

namespace Webkul\UVDesk\ExtensionBundle\App;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\ExtensionBundle\Framework\HelpdeskModuleInterface;
use Webkul\UVDesk\ExtensionBundle\Framework\HelpdeskComponentInterface;

class Configuration
{
	private $collection;

	public function __construct(ContainerInterface $container, RequestStack $requestStack)
	{
		$this->container = $container;
		$this->requestStack = $requestStack;
	}

	public function registerModule(HelpdeskModuleInterface $application, array $tags = []) : Configuration
	{
        $this->collection[] = $application;

        return $this;
	}

	public function registerComponent(HelpdeskComponentInterface $application, array $tags = []) : Configuration
	{
        $this->collection[] = $application;

        return $this;
	}

	public function getExtensionCollection() : array
	{
		return $this->collection;
	}

	public function configure() : Configuration
	{
		$twigLoader = $this->container->get('uvdesk_extension.twig_loader');

		$twigLoader->addPath('/home/users/akshay.kumar/Workstation/www/html/community-skeleton/apps/uvdesk/commons/Resources/views', '_uvdesk_extension_uvdesk_commons');
		$twigLoader->addPath('/home/users/akshay.kumar/Workstation/www/html/community-skeleton/apps/uvdesk/ecommerce/Resources/views', '_uvdesk_extension_uvdesk_ecommerce');
        $twigLoader->addPath('/home/users/akshay.kumar/Workstation/www/html/community-skeleton/apps/uvdesk/shopify/Resources/views', '_uvdesk_extension_uvdesk_shopify');

        return $this;
	}
}
