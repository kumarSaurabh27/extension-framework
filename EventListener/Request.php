<?php

namespace Webkul\UVDesk\ExtensionBundle\EventListener;

use Webkul\UVDesk\ExtensionBundle\App\Configuration;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class Request
{
    public function __construct(Configuration $kernel)
    {
        // Do nothing. This is to initialize the kernel service.
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        return;
    }
}
