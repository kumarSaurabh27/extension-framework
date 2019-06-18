<?php

namespace UVDeskApps\UVDesk\Shopify\Apps;

use Webkul\UVDesk\ExtensionBundle\Framework\CommunityApplication;

class OrderSyncronizer extends CommunityApplication
{
    public static function getName() : string
    {
        return "Shopify";
    }

    public static function getSummary() : string
    {
        return "Integrate support tickets with order details from your shopify store";
    }

    public static function getDescription() : string
    {
        return "Now build a connection between your Shopify Webstore and the inquiry of your Webstore Customers. Ask for the order ID on the ticket in the real time and see the order details on the ticket system only. Confirm the order related details by fetching it from the Shopify Webstore on the ticket for the validation and avoid fraudulent queries.";
    }

    public static function getFullyQualifiedName() : string
    {
        return "ecommerce-connector";
    }
}
