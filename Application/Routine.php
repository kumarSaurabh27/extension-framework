<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Application;

use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine\ApiRoutine;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine\RenderDashboardRoutine;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\RoutineInterface;

class Routine
{
    public function __construct()
    {

    }

    public function configureRoutine(RoutineInterface $routine)
    {
        dump($routine);
        die;
    }

    public static function create($name)
    {
        switch ($name) {
            case ApiRoutine::NAME:
                break;
            case RenderDashboardRoutine::NAME:
                break;
            default:
                throw new \Exception("Event not found : '$name'");
                break;
        }
    }
}
