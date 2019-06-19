<?php

namespace Webkul\UVDesk\ExtensionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Webkul\UVDesk\ExtensionBundle\Extensions\CommunityExtensionsManager;

class Application extends Controller
{
    public function loadDashboard(Request $request)
    {
        return $this->render('@UVDeskExtension//dashboard.html.twig', []);
    }

    public function loadApplicationDashboard($vendor, $extension, $application, Request $request)
    {
        $extensionsManager = $this->get('uvdesk.extensibles')->getRegisteredExtension(CommunityExtensionsManager::class);

        try {
            $application = $extensionsManager->getRegisteredApplication($vendor, $extension, $application);
            dump($application);
        } catch (\Exception $e) {
            dump('No result found');
        }

        die;

        return $this->render('@UVDeskExtension//applicationDashboard.html.twig', [
            'application' => $application
        ]);
    }
}
