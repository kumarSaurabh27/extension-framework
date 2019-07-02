<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce\Apps;

use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\Application;

class ECommerceOrders extends Application
{
    public static function getName() : string
    {
        return "ECommerce";
    }

    public static function getSummary() : string
    {
        return "Import ecommerce order details to your support tickets from different available platforms";
    }

    public static function getDescription() : string
    {
        return "Improve the efficiency of your support staff by displaying the order related details on the ticket system. It reduces the time spent by the support staff by fetching the order related details on the ticket system only. No need to leave ticket system to check the details.";
    }

    public static function getQualifiedName() : string
    {
        return "ecommerce-orders";
    }
}