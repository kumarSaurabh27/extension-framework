<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Applications;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationMetadata;

class ECommerceMetadata extends ApplicationMetadata
{
    public function getName() : string
    {
        return "Shopify";
    }

    public function getSummary() : string
    {
        return "Integrate support tickets with order details from your shopify store";
    }

    public function getDescription() : string
    {
        return "Now build a connection between your Shopify Webstore and the inquiry of your Webstore Customers. Ask for the order ID on the ticket in the real time and see the order details on the ticket system only. Confirm the order related details by fetching it from the Shopify Webstore on the ticket for the validation and avoid fraudulent queries.";
    }

    public function getQualifiedName() : string
    {
        return "ecommerce-connector";
    }

    public function getDashboardTemplate() : string
    {
        return '@_uvdesk_extension_akshay_shopify/apps/ecommerce/dashboard.html.twig';
    }
}
