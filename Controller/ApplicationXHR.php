<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\ExtensionManager;

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
                    'vendor' => $application->getExtension()->getPackage()->getVendor(),
                    'package' => $application->getExtension()->getPackage()->getPackage(),
                ],
            ];
        }, $this->get('uvdesk.extensibles')->getRegisteredComponent(ExtensionManager::class)->getApplications());

        return new JsonResponse($collection);
    }
}
