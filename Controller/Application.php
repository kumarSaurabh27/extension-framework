<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Webkul\UVDesk\ExtensionFrameworkBundle\Extensions\PackageManager;

use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine\ApiRoutine;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine\RenderDashboardRoutine;

class Application extends Controller
{
    public function dashboard($vendor, $extension, $application, Request $request)
    {
        $application = $this->get('uvdesk.extensibles')->getRegisteredComponent(PackageManager::class)->getApplicationByAttributes($vendor, $extension, $application);
        
        dump($application);
        die;

        // $dispatcher = new EventDispatcher();        
        // $dispatcher->addSubscriber($application);
        // $dispatcher->dispatch(RenderDashboardRoutine::NAME, ($event = Routine::create(RenderDashboardRoutine::NAME)));

        // dump($event);
        // die;

        // return $this->render('@ExtensionFramework//applicationDashboard.html.twig', [
        //     'application' => $application
        // ]);
    }

    public function apiEndpointXHR($vendor, $extension, $application, Request $request)
    {
        $application = $this->get('uvdesk.extensibles')->getRegisteredComponent(PackageManager::class)->getApplicationByAttributes($vendor, $extension, $application);

        dump($application);
        die;

        // $dispatcher = new EventDispatcher();        
        // $dispatcher->addSubscriber($application);
        // $dispatcher->dispatch(ApiRoutine::NAME, ($event = Routine::create(ApiRoutine::NAME)));

        // dump($event);
        // die;

        // return new JsonResponse([]);
    }
}
