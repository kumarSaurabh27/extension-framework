<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce\Utils\Platforms;

use UVDesk\CommunityPackages\UVDesk\ECommerce\Utils\ECommercePlatformInterface;
use UVDesk\CommunityPackages\UVDesk\ECommerce\Utils\ECommerceChannelConfigurationInterface;
use UVDesk\CommunityPackages\UVDesk\ECommerce\Utils\Platforms\Shopify\ChannelConfiguration;

class Shopify implements ECommercePlatformInterface
{
    private $channels = [];

    public function __construct(array $configs = [])
    {
        // // Initialize configurations
        // foreach ($configs['stores'] as $attributes) {
        //     ($store = new ChannelConfiguration($attributes['id']))
        //         ->setName($attributes['name'])
        //         ->setDomain($attributes['domain'])
        //         ->setClient($attributes['client'])
        //         ->setPassword($attributes['password'])
        //         ->setTimezone($attributes['timezone'])
        //         ->setIanaTimezone($attributes['iana_timezone'])
        //         ->setCurrencyFormat($attributes['currency_format'])
        //         ->setIsEnabled($attributes['enabled']);
            
        //     dump($store);
        //     // $this->shopifyConfiguration->addStoreConfiguration($store);
        //     die;
        // }
    }

    public static function getName() : string
    {
        return 'Shopify';
    }

    public static function getQualifiedName() : string
    {
        return 'shopify';
    }

    public static function getDescription() : string
    {
        return 'Shopify description';
    }

    public function create(array $attributes) : ECommerceChannelConfigurationInterface
    {
        ($channelConfiguration = new ChannelConfiguration())
            ->setDomain($attributes['domain'])
            ->setClient($attributes['api_key'])
            ->setPassword($attributes['api_password'])
            ->setIsEnabled((bool) $attributes['enabled']);

        if (false == $channelConfiguration->load()) {
            throw new \Exception('An error occurred while verifying your credentials. Please check the entered details.');
        }

        $this->channels[$attributes['domain']] = $channelConfiguration;
        return $channelConfiguration;
    }

    public function update(array $attributes) : ECommerceChannelConfigurationInterface
    {
        ($channelConfiguration = new ChannelConfiguration())
            ->setDomain($attributes['domain'])
            ->setClient($attributes['api_key'])
            ->setPassword($attributes['api_password'])
            ->setIsEnabled((bool) $attributes['enabled']);
        
        if (false == $channelConfiguration->load()) {
            throw new \Exception('An error occurred while verifying your credentials. Please check the entered details.');
        }

        $this->channels[$attributes['domain']] = $channelConfiguration;
        return $channelConfiguration;
    }

    public function remove(array $attributes) : ECommerceChannelConfigurationInterface
    {
        // ($channelConfiguration = new ChannelConfiguration())
        //     ->setDomain($attributes['domain'])
        //     ->setClient($attributes['api_key'])
        //     ->setPassword($attributes['api_password'])
        //     ->setIsEnabled((bool) $attributes['enabled']);

        $channelConfiguration = $this->channels[$attributes['domain']];
        unset($this->channels[$attributes['domain']]);
        return $channelConfiguration;
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
