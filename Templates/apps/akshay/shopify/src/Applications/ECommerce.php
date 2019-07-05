<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Applications;

use Webkul\UVDesk\CoreFrameworkBundle\Dashboard\Dashboard;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationMetadata;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine\ApiRoutine;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine\RenderDashboardRoutine;

class ECommerce extends Application
{
    public function __construct(ContainerInterface $container)
	{
        $this->container = $container;
    }

    public static function getMetadata() : ApplicationMetadata
    {
        return new ECommerceMetadata();
    }

    public static function getSubscribedEvents()
    {
        return array(
            ApiRoutine::NAME => array(
                array('handleApiRequest'),
            ),
            RenderDashboardRoutine::NAME => array(
                array('prepareDashboard'),
            ),
        );
    }

    public function prepareDashboard(RenderDashboardRoutine $event)
    {
        $event->getDashboardExtension();

        $dashboardExtension = $this->extendableComponentManager->getRegisteredComponent(Dashboard::class);

        $dashboardTemplate = $dashboardExtension->getDashboardTemplate();
        $dashboardTemplate->appendStylesheet('bundles/extensionframework/extensions/akshay/shopify/css/main.css');
        $dashboardTemplate->appendJavascript('bundles/extensionframework/extensions/akshay/shopify/js/main.js');
    }

    public function handleApiRequest(ApiRoutine $event)
    {
        dump($event);
        dump($this->package->getConfigurations());
        die;
    }
}
