<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce\Utils;

class ECommerceConfiguration
{
    const DEFAULT_TEMPLATE = __DIR__ . "/../../templates/configs/defaults.yaml";
    const CONFIGURATION_TEMPLATE = __DIR__ . "/../../templates/configs/configs.php";

    private $eCommercePlatforms = [];

    public function addECommercePlatform(ECommercePlatformInterface $eCommercePlatform)
    {
        $this->eCommercePlatforms[$eCommercePlatform::getQualifiedName()] = $eCommercePlatform;

        return $this;
    }

    public function getECommercePlatforms()
    {
        return $this->eCommercePlatforms;
    }

    public function getECommercePlatformByQualifiedName($qualifiedName)
    {
        return $this->eCommercePlatforms[$qualifiedName] ?? null;
    }
}
