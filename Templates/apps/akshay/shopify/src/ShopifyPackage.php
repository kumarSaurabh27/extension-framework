<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify;

use Webkul\UVDesk\ExtensionFrameworkBundle\Package\ExecutablePackage;

class ShopifyPackage extends ExecutablePackage
{
    public function install()
    {
        $this->copyConfiguration(__DIR__ . "/../templates/config.yaml");
        // $this->copyConfiguration(__DIR__ . "/../templates/config.yaml", 'dev');
    }
}
