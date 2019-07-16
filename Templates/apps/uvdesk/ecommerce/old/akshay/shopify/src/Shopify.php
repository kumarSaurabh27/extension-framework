<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Module;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use UVDesk\CommunityPackages\Akshay\Shopify\DependencyInjection\Configuration;

class Shopify extends Module
{
    public function getConfiguration() : ?ConfigurationInterface
    {
        return new Configuration();
    }

    public function getServices()
    {
        return __DIR__ . "/Resources/config/services.yaml";
    }

    public function getPackageReference() : string
    {
        return ShopifyPackage::class;
    }

    public function getApplicationReferences() : array
    {
        return array(Applications\ECommerce::class);
    }
}
