<?php

namespace Webkul\UVDesk\ExtensionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Webkul\UVDesk\ExtensionBundle\Extensions\CommunityExtensionsManager;
use UVDeskApps\UVDesk\Shopify\Apps\OrderSyncronizer;
use UVDeskApps\UVDesk\Shopify\Shopify;

class Application extends Controller
{
    public function loadDashboard(Request $request)
    {
        return $this->render('@UVDeskExtension//dashboard.html.twig', []);
    }

    public function loadApplicationDashboard($vendor, $extension, $application, Request $request)
    {
        $application = $this->get('uvdesk.extensibles')->getRegisteredExtension(CommunityExtensionsManager::class)->getApplicationByAttributes($vendor, $extension, $application);

        return $this->render('@UVDeskExtension//applicationDashboard.html.twig', [
            'application' => $application
        ]);
    }
}
