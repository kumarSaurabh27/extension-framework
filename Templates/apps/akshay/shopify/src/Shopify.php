<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify;

use UVDesk\CommunityPackages\Akshay\Shopify\Apps;
use Webkul\UVDesk\ExtensionFrameworkBundle\Module\Module;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use UVDesk\CommunityPackages\Akshay\Shopify\DependencyInjection\ShopifyConfiguration;

class Shopify extends Module
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

    public static function getConfiguration() : ?ConfigurationInterface
    {
        return new ShopifyConfiguration();
    }

    public function load()
    {

    }
}
