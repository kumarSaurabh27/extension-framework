<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Applications;

use Webkul\UVDesk\CoreFrameworkBundle\Dashboard\Dashboard;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\Application;
use Webkul\UVDesk\ExtensionFrameworkBundle\Events\ApplicationEvents;
use Webkul\UVDesk\CoreFrameworkBundle\Framework\ExtendableComponentManager;
use UVDesk\CommunityPackages\Akshay\Shopify\EventListeners\ShopifyEventSubscriber;

class Shopify extends Application implements EventSubscriberInterface
{
    public function __construct(ContainerInterface $container, ExtendableComponentManager $extendableComponentManager)
	{
        $this->container = $container;
		$this->extendableComponentManager = $extendableComponentManager;
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
        return '@_uvdesk_extension_akshay_shopify//dashboard.html.twig';
    }

    public static function getSubscribedEvents()
    {
        return [
            ApplicationEvents::LOAD_DASHBOARD => [
                ['injectAssets'],
            ],
        ];
    }

    public function injectAssets()
    {
        $dashboardExtension = $this->extendableComponentManager->getRegisteredComponent(Dashboard::class);

        $dashboardTemplate = $dashboardExtension->getDashboardTemplate();
        $dashboardTemplate->appendStylesheet('bundles/extensionframework/extensions/uvdesk/shopify/css/main.css');
        $dashboardTemplate->appendJavascript('bundles/extensionframework/extensions/uvdesk/shopify/js/main.js');
    }
}
