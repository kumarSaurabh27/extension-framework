<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce\Applications;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationMetadata;

class ECommerceOrdersMetadata extends ApplicationMetadata
{
    public function getName() : string
    {
        return "ECommerce";
    }

    public function getSummary() : string
    {
        return "Import ecommerce order details to your support tickets from different available platforms";
    }

    public function getDescription() : string
    {
        return "Improve the efficiency of your support staff by displaying the order related details on the ticket system. It reduces the time spent by the support staff by fetching the order related details on the ticket system only. No need to leave ticket system to check the details.";
    }

    public function getQualifiedName() : string
    {
        return "ecommerce-orders";
    }

    public function getDashboardTemplate() : string
    {
        return '@_uvdesk_extension_uvdesk_ecommerce/apps/ecommerce-orders/dashboard.html.twig';
    }
}
