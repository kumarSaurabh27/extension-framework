<?php

namespace UVDeskApps\UVDesk\Shopify;

use UVDeskApps\UVDesk\Shopify\Apps\ECommerce\ECommerce;
use Webkul\UVDesk\ExtensionBundle\Framework\CommunityModuleExtension;

final class Shopify extends CommunityModuleExtension
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
            ECommerce::class
        ];
    }
}
