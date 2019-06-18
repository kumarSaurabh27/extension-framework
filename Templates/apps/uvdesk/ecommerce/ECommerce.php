<?php

namespace UVDeskApps\UVDesk\ECommerce;

use UVDeskApps\UVDesk\ECommerce\Apps\ECommerceOrders;
use Webkul\UVDesk\ExtensionBundle\Framework\CommunityModuleExtension;

final class ECommerce extends CommunityModuleExtension
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
            ECommerceOrders::class,
        ];
    }
}
