<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Applications;

use Webkul\UVDesk\CoreFrameworkBundle\Dashboard\Dashboard;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationMetadata;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine\ApiRoutine;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine\RenderDashboardRoutine;
use UVDesk\CommunityPackages\Akshay\Shopify\Utils\Configuration\ShopifyConfiguration;
use UVDesk\CommunityPackages\Akshay\Shopify\Utils\Configuration\ShopifyStoreConfiguration;

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
        $dashboard = $event->getDashboardTemplate();
        $shopifyConfiguration = $this->getPackage()->getParsedConfigurations();

        // Add loadable resources to templates
        $dashboard->appendStylesheet('bundles/extensionframework/extensions/akshay/shopify/css/csspin.css');
        $dashboard->appendStylesheet('bundles/extensionframework/extensions/akshay/shopify/css/main.css');
        $dashboard->appendJavascript('bundles/extensionframework/extensions/akshay/shopify/js/main.js');

        // Configure dashboard
        $event
            ->setTemplateReference('@_uvdesk_extension_akshay_shopify/apps/ecommerce/dashboard.html.twig')
            ->addTemplateData('shopifyConfiguration', $shopifyConfiguration);
    }

    public function handleApiRequest(ApiRoutine $event)
    {
        $request = $event->getRequest();

        switch ($request->query->get('endpoint')) {
            case 'get-stores':
                $response = ['stores' => []];
                $shopifyConfiguration = $this->getPackage()->getParsedConfigurations();
                
                foreach ($shopifyConfiguration->getStoreConfigurations() as $configuration) {
                    $response['stores'] = [
                        'id' => $configuration->getId(),
                        'name' => $configuration->getName(),
                        'domain' => $configuration->getDomain(),
                        'enabled' => $configuration->getIsEnabled(),
                    ];
                }
                    
                $event->setResponseData($response);
                break;
            case 'save-store':
                $attributes = json_decode($request->getContent(), true);
                $shopifyConfiguration = $this->getPackage()->getParsedConfigurations();

                if ('POST' == $request->getMethod()) {
                    $storeConfiguration = new ShopifyStoreConfiguration();
                    $storeConfiguration
                        ->setDomain($attributes['domain'])
                        ->setClient($attributes['api_key'])
                        ->setPassword($attributes['api_password'])
                        ->setIsEnabled((bool) $attributes['enabled']);

                    if ($storeConfiguration->load()) {
                        $shopifyConfiguration->addStoreConfiguration($storeConfiguration);

                        // Update configurations
                        $package = $this->getPackage();
                        $package->updatePackageConfiguration((string) $shopifyConfiguration);
                    } else {
                        $event->setResponseCode(500);
                        $event->setResponseData([
                            'error' => 'An error occurred while verifying your credentials. Please check the entered details.'
                        ]);
                    }
                } else if ('PUT' == $request->getMethod()) {
                    $storeConfiguration = $shopifyConfiguration->getStoreConfiguration($attributes['domain']);
                    $storeConfiguration
                        ->setDomain($attributes['domain'])
                        ->setClient($attributes['api_key'])
                        ->setPassword($attributes['api_password'])
                        ->setIsEnabled((bool) $attributes['enabled']);
                    
                    if ($storeConfiguration->load()) {
                        // Update configurations
                        $package = $this->getPackage();
                        $package->updatePackageConfiguration((string) $shopifyConfiguration);
                    } else {
                        $event->setResponseCode(500);
                        $event->setResponseData([
                            'error' => 'An error occurred while verifying your credentials. Please check the entered details.'
                        ]);
                    }
                }
                break;
            default:
                break;
        }
    }
}
