<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Webkul\UVDesk\ExtensionFrameworkBundle\Extensions\PackageManager;

class ApplicationXHR extends Controller
{
    public function loadCollectionXHR(Request $request)
    {
        $collection = array_map(function ($application) {
            $metadata = $application->getMetadata();
            $packageMetadata = $application->getPackage()->getMetadata();

            return [
                'icon' => $metadata->getIcon(),
                'name' => $metadata->getName(),
                'summary' => $metadata->getSummary(),
                'qname' => $metadata->getQualifiedName(),
                'reference' => [
                    'vendor' => $packageMetadata->getVendor(),
                    'package' => $packageMetadata->getPackage(),
                ],
            ];
        }, $this->get('uvdesk.extensibles')->getRegisteredComponent(PackageManager::class)->getApplications());

        return new JsonResponse($collection);
    }
}
