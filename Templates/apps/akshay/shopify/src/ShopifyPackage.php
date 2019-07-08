<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\PackageMetadata;
use UVDesk\CommunityPackages\Akshay\Shopify\Utils\ShopifyConfiguration;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ConfigurablePackage;
use UVDesk\CommunityPackages\Akshay\Shopify\Utils\Metadata\StoreConfiguration;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ConfigurablePackageInterface;

class ShopifyPackage extends ConfigurablePackage implements ConfigurablePackageInterface
{
    private $shopifyConfiguration;

    public static function install(PackageMetadata $metadata) : void
    {
        self::updatePackageConfiguration($metadata, file_get_contents(__DIR__ . "/../templates/defaults.yaml"));
    }

    public function getParsedConfigurations() : ShopifyConfiguration
    {
        if (empty($this->shopifyConfiguration)) {
            $this->shopifyConfiguration = new ShopifyConfiguration();
            
            // Read configurations from package config.
            $configs = $this->getConfigurations();

            // Add stores to configuration
            foreach ($configs['stores'] as $attributes) {
                ($store = new StoreConfiguration())
                    ->setDomain($attributes['domain'])
                    ->setApiKey($attributes['domain'])
                    ->setApiPassword($attributes['domain']);
                
                $this->shopifyConfiguration->addStoreConfiguration($store);
            }
        }

        return $this->shopifyConfiguration;
    }
}
