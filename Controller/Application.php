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
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine\RenderDashboardRoutine;

class Application extends Controller
{
    public function dashboard(ApplicationInterface $application, RenderDashboardRoutine $event)
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($application);
        $dispatcher->dispatch($event, $event::getName());

        // Get event response
        $templateData = array_merge(['application' => $application], $event->getTemplateData());

        return $this->render('@ExtensionFramework//applicationDashboard.html.twig', $templateData);
    }

    public function apiEndpointXHR(ApplicationInterface $application, ApiRoutine $event)
    {
        $dispatcher = new EventDispatcher();        
        $dispatcher->addSubscriber($application);
        $dispatcher->dispatch(ApiRoutine::NAME, ($event = Routine::create(ApiRoutine::NAME)));

        dump($event);
        die;

        // return new JsonResponse([]);
    }
}
