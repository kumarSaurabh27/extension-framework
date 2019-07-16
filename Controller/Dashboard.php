<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Webkul\UVDesk\ExtensionFrameworkBundle\Utils\Applications;

class Dashboard extends Controller
{
    public function applications(Request $request)
    {
        return $this->render('@ExtensionFramework//dashboard.html.twig', []);
    }

    public function applicationsXHR(Request $request, Applications $applications)
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
        }, $applications->getCollection());

        return new JsonResponse($collection);
    }
}
