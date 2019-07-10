<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Utils\Configuration;

class ShopifyConfiguration
{
    const DEFAULT_TEMPLATE = __DIR__ . "/../../templates/defaults.yaml";
    const CONFIGURATION_TEMPLATE = __DIR__ . "/../../templates/configs.php";

    private $storeConfigurations = [];

    public function addStoreConfiguration(ShopifyStoreConfiguration $configuration)
    {
        $this->storeConfigurations[$configuration->getDomain()] = $configuration;

        return $this;
    }

    public function getStoreConfigurations() : array
    {
        return $this->storeConfigurations;
    }

    public function getStoreConfiguration($domain) : ?ShopifyStoreConfiguration
    {
        return $this->storeConfigurations[$domain] ?? null;
    }

    public function removeStoreConfiguration(ShopifyStoreConfiguration $configuration)
    {
        if (!empty($this->storeConfigurations[$configuration->getDomain()])) {
            unset($this->storeConfigurations[$configuration->getDomain()]);
        }

        return $this;
    }

    public function __toString()
    {
        if (!empty($this->storeConfigurations)) {
            $template = require __DIR__ . "/../../../templates/configs/configuration.php";

            $stream = array_reduce($this->storeConfigurations, function($stream, $storeConfiguration) {
                return $stream . (string) $storeConfiguration;
            }, '');
    
            return strtr($template, [
                '[[ STORES ]]' => $stream,
            ]);
        }

        return file_get_contents(__DIR__ . "/../../../templates/configs/defaults.yaml");
    }
}
