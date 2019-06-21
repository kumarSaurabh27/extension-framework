<?php

namespace UVDeskApps\UVDesk\Shopify\Apps\ECommerce;

use Twig\Environment as TwigEnvironment;
use Symfony\Component\HttpFoundation\RequestStack;
use Webkul\UVDesk\ExtensionBundle\Framework\CommunityApplication;

class ECommerce extends CommunityApplication
{
    public function __construct(RequestStack $requestStack, TwigEnvironment $twig)
    {
        $this->twig = $twig;
        $this->requestStack = $requestStack;
    }

    public static function getName() : string
    {
        return "Shopify ECommerce";
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
}
