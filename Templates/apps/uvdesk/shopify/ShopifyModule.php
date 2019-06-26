<?php

namespace UVDesk\CommunityExtension\UVDesk\ShopifyModule;

use UVDesk\CommunityExtension\UVDesk\ShopifyModule\Apps;
use Webkul\UVDesk\ExtensionFrameworkBundle\Module\Module;
// use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Application;

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
