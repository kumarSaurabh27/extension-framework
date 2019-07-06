<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Application\Routine;

use Symfony\Component\EventDispatcher\Event;
use Webkul\UVDesk\ExtensionFrameworkBundle\Application\RoutineInterface;

class ApiRoutine extends Event implements RoutineInterface
{
    const NAME = 'uvdesk_extensions.application_routine.handle_api_request';

    public static function getName() : string
    {
        return self::NAME;
    }
}
