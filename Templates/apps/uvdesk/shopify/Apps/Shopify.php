<?php

namespace UVDesk\CommunityExtension\UVDesk\ShopifyModule\Apps;

use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\Application;
use UVDesk\CommunityExtension\UVDesk\ShopifyModule\EventListeners\ShopifyEventSubscriber;

class Shopify extends Application
{
    public function __construct(ShopifyEventSubscriber $subscriber)
	{
		$this->subscriber = $subscriber;
    }

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

    public static function getQualifiedName() : string
    {
        return "ecommerce-connector";
    }

    public function getTemplate()
    {
        return '@_uvdesk_extension_uvdesk_shopify//dashboard.html.twig';
    }

    public function getEventSubscriber()
    {
        return $this->subscriber;
    }
}
