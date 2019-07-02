<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce;

use UVDesk\CommunityPackages\UVDesk\ECommerce\Apps;
use Webkul\UVDesk\ExtensionFrameworkBundle\Module\Module;

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
            Apps\ECommerceOrders::class,
        ];
    }
}
