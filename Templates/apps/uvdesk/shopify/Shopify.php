<?php

namespace UVDeskApps\UVDesk\Shopify;

use Webkul\UVDesk\ExtensionBundle\Framework\HelpdeskModule;

class Shopify extends HelpdeskModule
{
    public static function getTitle() : string
    {
        return "Shopify ECommerce";
    }

    public static function getDescription() : string
    {
        return "Shopify ECommerce Extension";
    }

    public static function services() : array
    {
        return [
            '/Resources/config/services.yaml'
        ];
    }
}
