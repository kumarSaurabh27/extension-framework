<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\PackageMetadata;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ConfigurablePackage;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ConfigurablePackageInterface;
use UVDesk\CommunityPackages\Akshay\Shopify\Utils\Configuration\ShopifyConfiguration;
use UVDesk\CommunityPackages\Akshay\Shopify\Utils\Configuration\ShopifyStoreConfiguration;

class ShopifyPackage extends ConfigurablePackage implements ConfigurablePackageInterface
{
    private $shopifyConfiguration;

    public function install() : void
    {
        $this->updatePackageConfiguration(file_get_contents(__DIR__ . "/../templates/configs/defaults.yaml"));
    }

    public function getParsedConfigurations() : ShopifyConfiguration
    {
        if (empty($this->shopifyConfiguration)) {
            $this->shopifyConfiguration = new ShopifyConfiguration();
            
            // Read configurations from package config.
            foreach ($this->getConfigurations()['stores'] as $attributes) {
                ($store = new ShopifyStoreConfiguration($attributes['id']))
                    ->setName($attributes['name'])
                    ->setDomain($attributes['domain'])
                    ->setClient($attributes['client'])
                    ->setPassword($attributes['password'])
                    ->setTimezone($attributes['timezone'])
                    ->setIanaTimezone($attributes['iana_timezone'])
                    ->setCurrencyFormat($attributes['currency_format'])
                    ->setIsEnabled($attributes['enabled']);
                
                $this->shopifyConfiguration->addStoreConfiguration($store);
            }
        }

        return $this->shopifyConfiguration;
    }
}
