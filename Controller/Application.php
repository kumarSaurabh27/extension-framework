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
}
