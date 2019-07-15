<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Configurators;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package\PackageInterface;

class PackageConfigurator
{
    public function configure(PackageInterface $package)
    {
        dump($package);
        die;
    }

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

		$package->setMetadata($metadata);
		$package->setConfigurations($configs);

		if ($package instanceof ConfigurablePackageInterface) {
			$package->setPathToConfigurationFile($this->pathToPackageConfigurations . "/" . str_replace('/', '_', $metadata->getName()) . ".yaml");
		}

		$this->packages[] = $package;
	}
}
