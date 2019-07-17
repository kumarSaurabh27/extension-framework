<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce\Applications;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application\Application;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application\ApplicationMetadata;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application\ApplicationInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine\ApiRoutine;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine\RenderDashboardRoutine;
use UVDesk\CommunityPackages\UVDesk\ECommerce\Utils\ECommerceConfiguration;

class ECommerceOrderSyncronization extends Application implements ApplicationInterface, EventSubscriberInterface
{
    public static function getMetadata() : ApplicationMetadata
    {
        return new ECommerceOrderSyncronizationMetadata();
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
        $configuration = $this->getPackage()->getParsedConfigurations();

        // Add loadable resources to templates
        $dashboard->appendStylesheet('bundles/extensionframework/extensions/uvdesk/ecommerce/css/csspin.css');
        $dashboard->appendStylesheet('bundles/extensionframework/extensions/uvdesk/ecommerce/css/main.css');
        $dashboard->appendJavascript('bundles/extensionframework/extensions/uvdesk/ecommerce/js/main.js');

        // Configure dashboard
        $event
            ->setTemplateReference('@_uvdesk_extension_uvdesk_ecommerce/apps/order-syncronization/dashboard.html.twig')
            ->addTemplateData('configuration', $configuration);
    }

    public function handleApiRequest(ApiRoutine $event)
    {
        $request = $event->getRequest();

        switch ($request->query->get('endpoint')) {
            case 'get-stores':
                $response = ['stores' => []];
                // $shopifyConfiguration = $this->getPackage()->getParsedConfigurations();
                
                // foreach ($shopifyConfiguration->getStoreConfigurations() as $configuration) {
                //     $response['stores'] = [
                //         'id' => $configuration->getId(),
                //         'name' => $configuration->getName(),
                //         'domain' => $configuration->getDomain(),
                //         'enabled' => $configuration->getIsEnabled(),
                //     ];
                // }
                    
                $event->setResponseData($response);
                break;
            case 'save-store':
                $attributes = json_decode($request->getContent(), true);
                $configuration = $this->getPackage()->getParsedConfigurations();
                
                $eCommercePlatform = $configuration->getECommercePlatformByQualifiedName('shopify');

                if (empty($eCommercePlatform)) {
                    dump('ecommerce platform not found');
                    die;
                }

                if ('POST' == $request->getMethod()) {
                    $channel = $eCommercePlatform->create($attributes);
                } else if ('PUT' == $request->getMethod()) {
                    $channel = $eCommercePlatform->update($attributes);
                }

                dump($eCommercePlatform);
                dump($channel);
                dump($configuration);
                die;

                $this->getPackage()->updateConfigurations((string) $configuration);
                break;
            default:
                break;
        }
    }
}
