<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Module;

use Webkul\UVDesk\ExtensionFrameworkBundle\Package\Package;
use Symfony\Component\Config\Definition\ConfigurationInterface;

abstract class Module implements ModuleInterface
{
    CONST EXTENSION_TYPE = 'uvdesk-module';

    protected $package;

    final public function __construct($name, $description, $source)
    {
        list($vendor, $package) = explode('/', $name);

        ($this->package = new Package())
            ->setName($name)
            ->setVendor($vendor)
            ->setPackage($package)
            ->setDescription($description)
            ->setType(self::EXTENSION_TYPE)
            ->setSource($source);
    }

    final public function getPackage() : Package
    {
        return $this->package;
    }

    public static function getServices() : array
    {
        return [];
    }

    public static function getApplications() : array
    {
        return [];
    }

    public static function getConfiguration() : ?ConfigurationInterface
    {
        return null;
    }
}
