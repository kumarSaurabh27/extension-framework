<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package\Package;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package\PackageInterface;

class ECommercePackage extends Package implements PackageInterface
{
    public function getPackage() : PackageInterface
    {
        return new ECommercePackage();
    }

    public function getServices()
    {
        return __DIR__ . "/Resources/config/services.yaml";
    }
}
