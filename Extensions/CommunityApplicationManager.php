<?php

namespace Webkul\UVDesk\ExtensionBundle\Extensions;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\CoreBundle\Extensibles\ExtendableComponentInterface;
use Webkul\UVDesk\ExtensionBundle\Framework\CommunityApplicationInterface;

/**
 * Extensibles: CommunityApplicationManager
 */
class CommunityApplicationManager implements ExtendableComponentInterface
{
	private $collection = [];

	public function __construct(ContainerInterface $container, RequestStack $requestStack, RouterInterface $router)
	{
		$this->router = $router;
		$this->container = $container;
		$this->requestStack = $requestStack;
	}

	public function registerApplication(CommunityApplicationInterface $application, $vendor, $extension)
	{
		$application::setVendor($vendor);
		$application::setExtension($extension);

		$this->collection[] = $application;
	}

	public function getApplicationCollection()
	{
		return $this->collection;
	}
}
