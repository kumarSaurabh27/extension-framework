<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Framework;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\CoreFrameworkBundle\Framework\ExtendableComponentInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\ApplicationInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Module\ModuleInterface;

class ExtensionManager implements ExtendableComponentInterface
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
			$extensionReflection = new \ReflectionClass($extension);

			$package = $extension->getPackage();
			$pathToExtensionsTwigResources = dirname($extensionReflection->getFileName()) . "/Resources/views";

			if (is_dir($pathToExtensionsTwigResources)) {
				$twigLoader->addPath($pathToExtensionsTwigResources, sprintf("_uvdesk_extension_%s_%s", $package->getVendor(), $package->getPackage()));
			}
		}
	}

	public function getExtensionResources() : array
	{
		$resources = [];
		
		foreach ($this->extensions as $extension) {
			$extensionReflection = new \ReflectionClass($extension);

			$package = $extension->getPackage();
			$pathToExtensionsTwigResources = dirname($extensionReflection->getFileName()) . "/Resources/public";
			
			if (is_dir($pathToExtensionsTwigResources)) {
				$resources[] = [
					'package' => $package->getName(),
					'path' => $pathToExtensionsTwigResources,
				];
			}
		}

		return $resources;
	}

	public function registerExtension(ModuleInterface $extension) : ExtensionManager
	{
		$this->extensions[get_class($extension)] = $extension;

		return $this;
	}

	public function registerApplication(ApplicationInterface $application)
	{
		$extension = $this->extensions[$application->getExtensionReference()];
		$package = $extension->getPackage();

		$this->applications[get_class($application)] = $application->setExtension($extension);
		$this->organizedCollection[$package->getVendor()][$package->getPackage()][$application->getQualifiedName()] = $application;

		return $this;
	}

	public function getApplications() : array
	{
		return array_values($this->applications);
	}

	public function getApplicationByReference($reference) : ApplicationInterface
	{
		if (empty($this->applications[$reference])) {
			throw new \Exception('No application found');
		}

		return $this->applications[$reference];
	}

	public function getApplicationByAttributes($vendor, $extension, $qualifiedName) : ApplicationInterface
	{
		if (empty($this->organizedCollection[$vendor][$extension])) {
			throw new \Exception(sprintf("No applications found under the %s/%s extension namespace", $vendor, $extension));
		} else if (empty($this->organizedCollection[$vendor][$extension][$qualifiedName])) {
			throw new \Exception(sprintf("No application %s found under the %s/%s extension namespace", $qualifiedName, $vendor, $extension));
		}

		return $this->organizedCollection[$vendor][$extension][$qualifiedName];
	}
}
