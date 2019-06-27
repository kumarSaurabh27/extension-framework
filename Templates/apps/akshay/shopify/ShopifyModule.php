<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify;

use UVDesk\CommunityPackages\Akshay\Shopify\Apps;
use Webkul\UVDesk\ExtensionFrameworkBundle\Module\Module;

class ShopifyModule extends Module
{
    public static function getServices() : array
    {
        return [
            __DIR__ . "/Resources/config/services.yaml"
        ];
    }

    public static function getApplications() : array
    {
        return [
            Apps\Shopify::class
        ];
    }
}
