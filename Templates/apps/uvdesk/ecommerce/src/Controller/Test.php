<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Test extends Controller
{
    public function private(Request $request)
    {
        dump('private');
        dump($request);
        die;
    }

    public function public(Request $request)
    {
        dump('public');
        dump($request);
        die;
    }
}
