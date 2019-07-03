<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify;

use Webkul\UVDesk\ExtensionFrameworkBundle\Package\Package;
use Webkul\UVDesk\ExtensionFrameworkBundle\Package\ExecutablePackageInterface;

class ShopifyPackage extends Package implements ExecutablePackageInterface
{
    public function install()
    {
        $this->copyConfiguration(__DIR__ . "/../templates/config.yaml");
        // $this->copyConfiguration(__DIR__ . "/../templates/config.yaml", 'dev');
    }
}
