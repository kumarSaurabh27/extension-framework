<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Events\Application;

class Routine
{
    const PREPARE_DASHBOARD = 'uvdesk_extensions.application_routine.prepare_dashboard';

    const HANDLE_API_REQUEST = 'uvdesk_extensions.application_routine.handle_api_request';

    const HANDLE_CALLBACK_REQUEST = 'uvdesk_extensions.application_routine.handle_callback_request';
}
