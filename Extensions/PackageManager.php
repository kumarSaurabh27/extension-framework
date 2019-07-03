<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Extensions;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ModuleInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\PackageMetadata;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\PackageInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationInterface;
use Webkul\UVDesk\CoreFrameworkBundle\Framework\ExtendableComponentInterface;

class PackageManager implements ExtendableComponentInterface
{
	private $packages = [];
	private $organizedCollection = [];

	public function __construct(ContainerInterface $container, RequestStack $requestStack, RouterInterface $router)
	{
		$this->router = $router;
		$this->container = $container;
		$this->requestStack = $requestStack;
	}

	public function autoconfigure()
	{
		$twigLoader = $this->container->get('uvdesk_extension.twig_loader');

		foreach ($this->packages as $package) {
			$metadata = $package->getMetadata();
			$class = new \ReflectionClass($package);

			$pathToExtensionsTwigResources = dirname($class->getFileName()) . "/Resources/views";

			if (is_dir($pathToExtensionsTwigResources)) {
				$twigLoader->addPath($pathToExtensionsTwigResources, sprintf("_uvdesk_extension_%s_%s", $metadata->getVendor(), $metadata->getPackage()));
			}
		}
	}

	// public function getExtensionResources() : array
	// {
	// 	$resources = [];
		
	// 	foreach ($this->extensions as $extension) {
	// 		$extensionReflection = new \ReflectionClass($extension);

	// 		$package = $extension->getPackage();
	// 		$pathToExtensionsTwigResources = dirname($extensionReflection->getFileName()) . "/Resources/public";
			
	// 		if (is_dir($pathToExtensionsTwigResources)) {
	// 			$resources[] = [
	// 				'package' => $package->getName(),
	// 				'path' => $pathToExtensionsTwigResources,
	// 			];
	// 		}
	// 	}

	// 	return $resources;
	// }

	public function configurePackage($root, array $attributes, array $configs, PackageInterface $package)
	{
		($metadata = new PackageMetadata())
			->setRoot($root)
			->setName($attributes['name'])
			->setDescription($attributes['description'])
			->setType($attributes['type'])
			->setDefinedNamespaces($attributes['autoload']);
		
		foreach ($attributes['extensions'] as $reference => $env) {
			$metadata->addExtensionReference($reference, $env);
		}

		foreach ($attributes['scripts'] as $reference) {
			$metadata->addScript($reference);
		}

		$package->setMetadata($metadata);
		$package->setConfigurations($configs);

		$this->packages[] = $package;
	}

	public function configureApplication(ApplicationInterface $application, PackageInterface $package)
	{
		$application->setPackage($package);
		
		$metadata = $application->getMetadata();
		$packageMetadata = $package->getMetadata();
		
		$this->applications[] = $application;
		$this->organizedCollection[$packageMetadata->getVendor()][$packageMetadata->getPackage()][$metadata->getQualifiedName()] = $application;
	}

	public function getApplications() : array
	{
		return $this->applications;
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
