<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Utils;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\MappingResource;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application\ApplicationInterface;

class Applications
{
	public function __construct(ContainerInterface $container, MappingResource $mappingResource, Packages $packages)
	{
		$this->packages = $packages;
		$this->container = $container;
		$this->mappingResource = $mappingResource;
	}

	public function getCollection()
	{
		$applications = [];

		foreach ($this->mappingResource->getApplications() as $id => $tags) {
			$applications[] = $this->container->get($id);
		}

		return $applications;
	}

	public function getApplicationByAttributes($vendor, $package, $name)
	{
		dump($vendor, $package, $name);

		dump($this->packages);
		dump($this->mappingResource);
		die;
	}
}
