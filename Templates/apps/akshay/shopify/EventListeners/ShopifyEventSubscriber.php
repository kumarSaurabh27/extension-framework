<?php

namespace UVDesk\CommunityPackages\UVDesk\ShopifyModule\EventListeners;

use UVDesk\CommunityPackages\UVDesk\ShopifyModule\Apps\Shopify;
use Webkul\UVDesk\CoreFrameworkBundle\Dashboard\Dashboard;
use Webkul\UVDesk\ExtensionFrameworkBundle\Events\ApplicationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webkul\UVDesk\CoreFrameworkBundle\Framework\ExtendableComponentManager;
use Webkul\UVDesk\ExtensionFrameworkBundle\Events\ApplicationSubscriberInterface;

class ShopifyEventSubscriber implements EventSubscriberInterface
{
    public function __construct(ExtendableComponentManager $extendableComponentManager)
	{
		$this->extendableComponentManager = $extendableComponentManager;
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
