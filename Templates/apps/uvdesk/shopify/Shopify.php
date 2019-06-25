<?php

namespace UVDeskApps\UVDesk\Shopify;

use UVDeskApps\UVDesk\Shopify\Apps;
use Webkul\UVDesk\ExtensionBundle\Framework\Module;

final class Shopify extends Module
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
