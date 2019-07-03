<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Applications;

use Webkul\UVDesk\CoreFrameworkBundle\Dashboard\Dashboard;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationMetadata;
use Webkul\UVDesk\ExtensionFrameworkBundle\Events\ApplicationEvents;
use Webkul\UVDesk\CoreFrameworkBundle\Framework\ExtendableComponentManager;
use UVDesk\CommunityPackages\Akshay\Shopify\EventListeners\ShopifyEventSubscriber;

class ECommerce extends Application implements EventSubscriberInterface
{
    public function __construct(ContainerInterface $container, ExtendableComponentManager $extendableComponentManager)
	{
        $this->container = $container;
		$this->extendableComponentManager = $extendableComponentManager;
    }

    public static function getMetadata() : ApplicationMetadata
    {
        return new ECommerceMetadata();
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
