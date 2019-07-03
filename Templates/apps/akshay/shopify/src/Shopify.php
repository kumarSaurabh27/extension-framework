<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify;

use Webkul\UVDesk\ExtensionFrameworkBundle\Module\Module;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Shopify extends Module
{
    public function __construct()
    {

    }

    public function getConfiguration() : ?ConfigurationInterface
    {
        return new DependencyInjection\Configuration();
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
        return array(Applications\Shopify::class);
    }
}
