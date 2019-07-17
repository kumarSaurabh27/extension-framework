<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package\ConfigurablePackage;
use UVDesk\CommunityPackages\UVDesk\ECommerce\DependencyInjection\PackageConfiguration;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package\ConfigurablePackageInterface;

use UVDesk\CommunityPackages\UVDesk\ECommerce\Utils\Platforms\Shopify;
use UVDesk\CommunityPackages\UVDesk\ECommerce\Utils\ECommerceConfiguration;

class ECommercePackage extends ConfigurablePackage implements ConfigurablePackageInterface
{
    private $configuration;

    public function getConfiguration() : ?ConfigurationInterface
    {
        return new PackageConfiguration();
    }

    public function install() : void
    {
        $this->updatePackageConfiguration(file_get_contents(__DIR__ . "/../templates/configs/defaults.yaml"));
    }

    public function getParsedConfigurations()
    {
        // dump('parsing configurations');
        // dump($this->getConfigurationParameters());

        if (empty($this->configuration)) {
            $this->configuration = new ECommerceConfiguration();

            foreach ($this->getConfigurationParameters() as $platform => $attributes) {
                switch ($platform) {
                    case 'shopify':
                        $shopifyConfiguration = new Shopify($attributes);

                        $this->configuration->addECommercePlatform($shopifyConfiguration);
                        break;
                    default:
                        break;
                }
            }
        }

        return $this->configuration;
    }
}
