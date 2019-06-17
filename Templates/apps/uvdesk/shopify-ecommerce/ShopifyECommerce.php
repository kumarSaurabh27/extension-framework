<?php

namespace UVDeskApps\UVDesk\ShopifyECommerce;

use Webkul\UVDesk\ExtensionBundle\Apps\Application;

class ShopifyECommerce extends Application
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
