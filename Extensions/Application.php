<?php

namespace Webkul\UVDesk\ExtensionBundle\Extensions;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\CoreBundle\Extensibles\ExtendableComponentInterface;
use Webkul\UVDesk\ExtensionBundle\Extensions\Type\ApplicationInterface;

/**
 * Extensibles: Homepage
 */
class Application implements ExtendableComponentInterface
{
	private $segments = [];

	public function __construct(ContainerInterface $container, RequestStack $requestStack, RouterInterface $router)
	{
		$this->router = $router;
		$this->container = $container;
		$this->requestStack = $requestStack;
	}

	public function addSegment(ApplicationInterface $segment)
	{
		$this->segments[] = $segment;
	}
}
