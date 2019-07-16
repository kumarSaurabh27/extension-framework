<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Utils;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\MappingResource;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package\PackageMetadata;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package\PackageInterface;

class Packages
{
	public function __construct(ContainerInterface $container, MappingResource $mappingResource)
	{
		$this->container = $container;
		$this->mappingResource = $mappingResource;
	}

	public function getCollection()
	{
		dump('return package collection');
		die;
	}
}
