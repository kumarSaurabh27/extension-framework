<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Applications;

use Webkul\UVDesk\CoreFrameworkBundle\Dashboard\Dashboard;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application;
use Webkul\UVDesk\ExtensionFrameworkBundle\Events\Application\Routine;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationMetadata;
use Webkul\UVDesk\CoreFrameworkBundle\Framework\ExtendableComponentManager;
use UVDesk\CommunityPackages\Akshay\Shopify\ShopifyPackage;

class ECommerce extends Application implements EventSubscriberInterface
{
    public function __construct(ContainerInterface $container, ExtendableComponentManager $extendableComponentManager, ShopifyPackage $package)
	{
        $this->package = $package;
        $this->container = $container;
        $this->extendableComponentManager = $extendableComponentManager;
    }

    public static function getMetadata() : ApplicationMetadata
    {
        return new ECommerceMetadata();
    }

    public static function getSubscribedEvents()
    {
        return array(
            Routine::PREPARE_DASHBOARD => array(
                array('injectAssets'),
            ),
        );
    }

    public function injectAssets()
    {
        $dashboardExtension = $this->extendableComponentManager->getRegisteredComponent(Dashboard::class);

        $dashboardTemplate = $dashboardExtension->getDashboardTemplate();
        $dashboardTemplate->appendStylesheet('bundles/extensionframework/extensions/akshay/shopify/css/main.css');
        $dashboardTemplate->appendJavascript('bundles/extensionframework/extensions/akshay/shopify/js/main.js');
    }
}
