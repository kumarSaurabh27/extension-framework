<?php

namespace Webkul\UVDesk\ExtensionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Webkul\UVDesk\ExtensionBundle\Extensions\CommunityApplicationManager;

class ApplicationXHR extends Controller
{
    public function loadCollectionXHR(Request $request)
    {
        $collection = [];
        $applicationExtension = $this->get('uvdesk.extensibles')->getRegisteredExtension(CommunityApplicationManager::class);

        foreach ($applicationExtension->getApplicationCollection() as $application) {
            $collection[] = [
                'icon' => $application::getIcon(),
                'name' => $application::getName(),
                'summary' => $application::getSummary(),
                'qname' => $application::getFullyQualifiedName(),
                'reference' => [
                    'vendor' => $application::getVendor(),
                    'extension' => $application::getExtension(),
                ],
            ];
        }

        return new JsonResponse($collection);
    }
}
