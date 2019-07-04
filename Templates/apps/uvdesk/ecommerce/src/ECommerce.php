<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Module;

final class ECommerce extends Module
{
    public function getServices()
    {
        return __DIR__ . "/Resources/config/services.yaml";
    }

    public function getPackageReference() : string
    {
        return ECommercePackage::class;
    }

    public function getApplicationReferences() : array
    {
        return array(Apps\ECommerceOrders::class);
    }
}
