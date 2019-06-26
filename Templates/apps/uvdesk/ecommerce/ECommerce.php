<?php

namespace UVDesk\CommunityExtension\UVDesk\ECommerce;

use UVDesk\CommunityExtension\UVDesk\ECommerce\Apps\ECommerceOrders;
use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\Module;

final class ECommerce extends Module
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
