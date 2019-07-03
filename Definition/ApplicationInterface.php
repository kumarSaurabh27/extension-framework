<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition;

interface ApplicationInterface
{
    public static function getMetadata() : ApplicationMetadata;

    public function setPackage(PackageInterface $package) : ApplicationInterface;
    
    public function getPackage() : PackageInterface;
}
