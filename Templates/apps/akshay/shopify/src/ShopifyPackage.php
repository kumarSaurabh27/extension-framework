<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\PackageMetadata;
use UVDesk\CommunityPackages\Akshay\Shopify\Utils\ChannelConfiguration;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ConfigurablePackage;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ConfigurablePackageInterface;

class ShopifyPackage extends ConfigurablePackage implements ConfigurablePackageInterface
{
    public static function install(PackageMetadata $metadata) : void
    {
        self::updatePackageConfiguration($metadata, file_get_contents(__DIR__ . "/../templates/defaults.yaml"));
    }

    public function parseConfigurations()
    {
        dump($this->getConfigurations());
        die;
    }
}
