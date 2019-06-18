<?php

namespace Webkul\UVDesk\ExtensionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Application extends Controller
{
    public function loadDashboard(Request $request)
    {
        return $this->render('@UVDeskExtension//dashboard.html.twig', []);
    }

    public function loadApplicationDashboard($vendor, $extension, $application, Request $request)
    {
        dump($vendor);
        dump($extension);
        dump($application);
        dump($this->get('uvdesk.extensibles'));
        die;

        return $this->render('@UVDeskExtension//applicationDashboard.html.twig', []);
    }
}
