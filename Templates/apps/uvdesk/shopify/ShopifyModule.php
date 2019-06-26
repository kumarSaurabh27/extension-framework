<?php

namespace UVDesk\CommunityExtension\UVDesk\Shopify;

use UVDesk\CommunityExtension\UVDesk\Shopify\Apps;
use Webkul\UVDesk\ExtensionFrameworkBundle\Moudle\Module;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Application;

class Shopify extends Application
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
