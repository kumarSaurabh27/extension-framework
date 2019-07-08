<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Utils;

use UVDesk\CommunityPackages\Akshay\Shopify\Utils\Metadata\StoreConfiguration;

final class ShopifyConfiguration
{
    const DEFAULT_TEMPLATE = __DIR__ . "/../../templates/defaults.yaml";
    const CONFIGURATION_TEMPLATE = __DIR__ . "/../../templates/configs.php";

    private $stores = [];

    public function addStoreConfiguration(StoreConfiguration $configuration)
    {
        $this->stores[$configuration->getDomain()] = $configuration;

        return $this;
    }

    public function getStoreConfigurations() : array
    {
        return $this->stores;
    }

    public function getStoreConfiguration($domain) : ?StoreConfiguration
    {
        return $this->stores[$domain] ?? null;
    }

    public function removeStoreConfiguration(StoreConfiguration $configuration)
    {
        if (!empty($this->stores[$configuration->getDomain()])) {
            unset($this->stores[$configuration->getDomain()]);
        }

        return $this;
    }

    public function __toString()
    {
        if (!empty($this->collection)) {
            $stream = array_reduce($this->collection, function($stream, $mailbox) {
                return $stream . (string) $mailbox;
            }, '');
    
            return strtr(require self::CONFIGURATION_TEMPLATE, [
                '[[ MAILBOXES ]]' => $stream,
            ]);
        }

        return file_get_contents(self::DEFAULT_TEMPLATE);
    }
}
