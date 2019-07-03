<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Events\ApplicationEvents;
use Webkul\UVDesk\ExtensionFrameworkBundle\Extensions\PackageManager;

class Application extends Controller
{
    public function loadDashboard(Request $request)
    {
        return $this->render('@ExtensionFramework//dashboard.html.twig', []);
    }

    public function loadApplicationDashboard($vendor, $extension, $application, Request $request)
    {
        dump($request);
        die;
        
        $application = $this->get('uvdesk.extensibles')->getRegisteredComponent(PackageManager::class)->getApplicationByAttributes($vendor, $extension, $application);

        if (empty($application)) {
            return new Response('', 404);
        }
        
        $dispatcher = new EventDispatcher();
        $event = new GenericEvent(ApplicationEvents::LOAD_DASHBOARD, array('request' => $request));

        $dispatcher->addSubscriber($application);
        $dispatcher->dispatch(ApplicationEvents::LOAD_DASHBOARD, $event);

        return $this->render('@ExtensionFramework//applicationDashboard.html.twig', [
            'application' => $application
        ]);
    }
}
