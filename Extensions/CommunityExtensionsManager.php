<?php

namespace Webkul\UVDesk\ExtensionBundle\Extensions;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\CoreBundle\Extensibles\ExtendableComponentInterface;
use Webkul\UVDesk\ExtensionBundle\Framework\CommunityApplicationInterface;
use Webkul\UVDesk\ExtensionBundle\Framework\CommunityModuleExtensionInterface;

/**
 * Extensibles: CommunityExtensionsManager
 */
class CommunityExtensionsManager implements ExtendableComponentInterface
{
	private $extensions = [];
	private $applications = [];
	private $collection = [];
	private $organizedCollection = [];

	public function __construct(ContainerInterface $container, RequestStack $requestStack, RouterInterface $router)
	{
		$this->router = $router;
		$this->container = $container;
		$this->requestStack = $requestStack;
	}

	public function autoconfigure()
	{
		dump('configuring extensions');
		dump($this->extensions);
		dump($this->applications);
		dump($this->organizedCollection);

		die;
	}

	public function registerExtension(CommunityModuleExtensionInterface $extension) : CommunityExtensionsManager
	{
		$this->extensions[get_class($extension)] = $extension;

		return $this;
	}

	public function registerApplication(CommunityApplicationInterface $application)
	{
		$extension = $this->extensions[$application->getExtensionReference()];

		$this->applications[] = $application->setExtension($extension);
		$this->organizedCollection[$extension->getVendor()][$extension->getPackage()][$application->getQualifiedName()] = $application;

		return $this;
	}

	public function getAvailableApplications($vendor, $extension)
	{
		if (empty($this->organizedCollection[$vendor][$extension])) {
			throw new \Exception(sprintf("No applications found under the %s/%s extension namespace", $vendor, $extension));
		}

		return $this->organizedCollection[$vendor][$extension];
	}

	public function getRegisteredApplication($vendor, $extension, $qualifiedName)
	{
		if (empty($this->organizedCollection[$vendor][$extension][$qualifiedName])) {
			throw new \Exception(sprintf("No application found under the %s/%s extension namespace", $vendor, $extension));
		}

		return $this->organizedCollection[$vendor][$extension][$qualifiedName];
	}

	public function getApplicationCollection()
	{
		return $this->collection;
	}
}
