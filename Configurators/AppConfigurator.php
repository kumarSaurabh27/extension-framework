<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Configurators;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application\ApplicationInterface;

class AppConfigurator
{
    public function configure(ApplicationInterface $application)
    {
        dump($application);
        die;
    }

    // public function configureApplication(ApplicationInterface $application, PackageInterface $package)
	// {
	// 	$application->setPackage($package);
		
	// 	$metadata = $application->getMetadata();
	// 	$packageMetadata = $package->getMetadata();
		
	// 	$this->applications[] = $application;
	// 	$this->organizedCollection[$packageMetadata->getVendor()][$packageMetadata->getPackage()][$metadata->getQualifiedName()] = $application;
	// }
}
