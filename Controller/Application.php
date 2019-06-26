<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Webkul\UVDesk\ExtensionFrameworkBundle\Events\ApplicationEvents;
use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\ExtensionManager;

class Application extends Controller
{
    public function loadDashboard(Request $request)
    {
        return $this->render('@ExtensionFramework//dashboard.html.twig', []);
    }

    public function loadApplicationDashboard($vendor, $extension, $application, Request $request)
    {
        $dispatcher = new EventDispatcher();
        $application = $this->get('uvdesk.extensibles')->getRegisteredComponent(ExtensionManager::class)->getApplicationByAttributes($vendor, $extension, $application);

        $dispatcher->addSubscriber($application->getEventSubscriber());

        $event = new GenericEvent(ApplicationEvents::LOAD_DASHBOARD, [
            'request' => $request,
        ]);
        
        $dispatcher->dispatch(ApplicationEvents::LOAD_DASHBOARD, $event);
        // die;

        // $event = new ApplicationEvent($application);
        // $dispatcher->disptach($event::NAME, $event);
        // dump($event);
        // die;

        return $this->render('@ExtensionFramework//applicationDashboard.html.twig', [
            'application' => $application
        ]);
    }
}
