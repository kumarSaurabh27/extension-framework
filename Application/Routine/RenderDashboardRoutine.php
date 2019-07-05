<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine;

use Webkul\UVDesk\CoreFrameworkBundle\Dashboard\Dashboard;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\RoutineInterface;

class RenderDashboardRoutine implements RoutineInterface
{
    const NAME = 'uvdesk_extensions.application_routine.prepare_dashboard';

    public function getDashboardExtension() : ?Dashboard
    {
        return null;
    }
}
