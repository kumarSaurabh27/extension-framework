<?php

namespace Webkul\UVDesk\ExtensionBundle\Framework;

abstract class Module implements ModuleInterface
{
    protected $vendor;
    protected $package;
    protected $directory;

    final public function __construct(string $vendor, string $package, string $directory)
    {
        $this->vendor = $vendor;
        $this->package = $package;
        $this->directory = $directory;
    }

    final public function getVendor() : string
    {
        return strtolower($this->vendor);
    }

    final public function getPackage() : string
    {
        return strtolower($this->package);
    }

    final public function getDirectory() : string
    {
        return $this->directory;
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
