<?php

namespace Webkul\UVDesk\ExtensionBundle\Framework;

abstract class CommunityModuleExtension implements CommunityModuleExtensionInterface
{
    protected $vendor;
    protected $package;

    final public function __construct($vendor, $package)
    {
        $this->vendor = $vendor;
        $this->package = $package;
    }

    final public function getVendor() : string
    {
        return $this->vendor;
    }

    final public function getPackage() : string
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
}
