<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Applications;

use Webkul\UVDesk\CoreFrameworkBundle\Dashboard\Dashboard;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationMetadata;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Event\ApiRoutine;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Event\RenderDashboardRoutine;

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
            ApiRoutine::getName() => array(
                array('handleApiRequest'),
            ),
            RenderDashboardRoutine::getName() => array(
                array('prepareDashboard'),
            ),
        );
    }

    public function prepareDashboard(RenderDashboardRoutine $event)
    {
        $dashboardTemplate = $event->getDashboardTemplate();

        // Add loadable resources to templates
        $dashboardTemplate->appendStylesheet('bundles/extensionframework/extensions/akshay/shopify/css/main.css');
        $dashboardTemplate->appendJavascript('bundles/extensionframework/extensions/akshay/shopify/js/main.js');

        // Configure dashboard
        $event
            ->setTemplateReference('@_uvdesk_extension_akshay_shopify/apps/ecommerce/dashboard.html.twig')
            ->addTemplateData('channels', $this->getPackage()->parseConfigurations());
    }

    public function handleApiRequest(ApiRoutine $event)
    {
        dump($event);
        dump($this->package->getConfigurations());
        die;
    }
}
