<?php

namespace Webkul\UVDesk\ExtensionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Webkul\UVDesk\ExtensionBundle\Extensions\CommunityExtensionsManager;

class ApplicationXHR extends Controller
{
    public function loadCollectionXHR(Request $request)
    {
        $collection = array_map(function ($application) {
            return [
                'icon' => $application::getIcon(),
                'name' => $application::getName(),
                'summary' => $application::getSummary(),
                'qname' => $application::getQualifiedName(),
                'reference' => [
                    'vendor' => $application->getExtension()->getVendor(),
                    'package' => $application->getExtension()->getPackage(),
                ],
            ];
        }, $this->get('uvdesk.extensibles')->getRegisteredExtension(CommunityExtensionsManager::class)->getApplications());

        return new JsonResponse($collection);
    }
}
