<?php

namespace UVDeskApps\UVDesk\Shopify\EventListeners;

use UVDeskApps\UVDesk\Shopify\Apps\Shopify;
use Webkul\UVDesk\CoreBundle\Dashboard\Dashboard;
use Webkul\UVDesk\ExtensionBundle\Events\ApplicationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webkul\UVDesk\CoreBundle\Framework\ExtendableComponentManager;
use Webkul\UVDesk\ExtensionBundle\Events\ApplicationSubscriberInterface;

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
        $dashboardTemplate->appendStylesheet('bundles/uvdeskextension/extensions/uvdesk/shopify/css/main.css');
    }
}
