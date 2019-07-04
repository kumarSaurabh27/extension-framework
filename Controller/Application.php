<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Webkul\UVDesk\ExtensionFrameworkBundle\Extensions\PackageManager;
use Webkul\UVDesk\ExtensionFrameworkBundle\Events\Application\Routine;

class Application extends Controller
{
    public function dashboard($vendor, $extension, $application, Request $request)
    {
        $application = $this->get('uvdesk.extensibles')->getRegisteredComponent(PackageManager::class)->getApplicationByAttributes($vendor, $extension, $application);

        if (empty($application)) {
            return new Response('', 404);
        }
        
        $dispatcher = new EventDispatcher();
        $event = new GenericEvent(Routine::PREPARE_DASHBOARD, array('request' => $request));

        $dispatcher->addSubscriber($application);
        $dispatcher->dispatch(Routine::PREPARE_DASHBOARD, $event);

        return $this->render('@ExtensionFramework//applicationDashboard.html.twig', [
            'application' => $application
        ]);
    }

    public function apiEndpointXHR($vendor, $extension, $application, Request $request)
    {
        $application = $this->get('uvdesk.extensibles')->getRegisteredComponent(PackageManager::class)->getApplicationByAttributes($vendor, $extension, $application);

        if (empty($application)) {
            return new JsonResponse([], 404);
        }

        $dispatcher = new EventDispatcher();
        $event = new GenericEvent(Routine::HANDLE_API_REQUEST, array('request' => $request));

        $dispatcher->addSubscriber($application);
        $dispatcher->dispatch(Routine::HANDLE_API_REQUEST, $event);

        return new JsonResponse([]);
    }

    public function callbackEndpointXHR(Request $request)
    {
        return new JsonResponse([]);
    }
}
