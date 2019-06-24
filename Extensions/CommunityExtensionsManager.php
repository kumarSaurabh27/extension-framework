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
	private $organizedCollection = [];

	public function __construct(ContainerInterface $container, RequestStack $requestStack, RouterInterface $router)
	{
		$this->router = $router;
		$this->container = $container;
		$this->requestStack = $requestStack;
	}

	public function autoconfigure()
	{
		// Load template paths
		$twigLoader = $this->container->get('uvdesk_extension.twig_loader');

		foreach ($this->extensions as $extension) {
			$pathToExtensionsTwigResources = $extension->getDirectory() . "/Resources/views";

			if (is_dir($pathToExtensionsTwigResources)) {
				$twigLoader->addPath($pathToExtensionsTwigResources, sprintf("_uvdesk_extension_%s_%s", $extension->getVendor(), $extension->getPackage()));
			}
		}
	}

	public function registerExtension(CommunityModuleExtensionInterface $extension) : CommunityExtensionsManager
	{
		$this->extensions[get_class($extension)] = $extension;

		return $this;
	}

	public function registerApplication(CommunityApplicationInterface $application)
	{
		$extension = $this->extensions[$application->getExtensionReference()];

		$this->applications[get_class($application)] = $application->setExtension($extension);
		$this->organizedCollection[$extension->getVendor()][$extension->getPackage()][$application->getQualifiedName()] = $application;

		return $this;
	}

	public function getApplications() : array
	{
		return array_values($this->applications);
	}

	public function getApplicationByReference($reference) : CommunityApplicationInterface
	{
		if (empty($this->applications[$reference])) {
			throw new \Exception('No application found');
		}

		return $this->applications[$reference];
	}

	public function getApplicationByAttributes($vendor, $extension, $qualifiedName) : CommunityApplicationInterface
	{
		if (empty($this->organizedCollection[$vendor][$extension])) {
			throw new \Exception(sprintf("No applications found under the %s/%s extension namespace", $vendor, $extension));
		} else if (empty($this->organizedCollection[$vendor][$extension][$qualifiedName])) {
			throw new \Exception(sprintf("No application %s found under the %s/%s extension namespace", $qualifiedName, $vendor, $extension));
		}

		return $this->organizedCollection[$vendor][$extension][$qualifiedName];
	}
}