<?php

namespace Webkul\UVDesk\ExtensionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApplicationXHR extends Controller
{
    public function loadCollectionXHR(Request $request)
    {
        dump($this->get('uvdesk.extensibles'));
        die;
        
        // $results = [];
        // if($request->isXmlHttpRequest()) {
        //     $em = $this->getDoctrine()->getManager();
        //     $repository = $this->getDoctrine()->getRepository('WebkulAppBundle:Application');
        //     $results = $repository->getAllApplications($request->query, $this->container);
        // }

        return new JsonResponse([]);
    }
}
