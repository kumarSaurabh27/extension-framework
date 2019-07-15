<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\PackageManager;
use Webkul\UVDesk\CoreFrameworkBundle\Framework\ExtendableComponentManager;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationInterface;

class Kernel
{
    public function __construct(PackageManager $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
    }

    public function onKernelControllerArguments(FilterControllerArgumentsEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        list($class, $method) = explode('::', $request->get('_controller'));

        $reflectionClass = new \ReflectionClass($class);
        
        if ($reflectionClass->hasMethod($method)) {
            $args = [];
            $controllerArguments = $event->getArguments();

            foreach ($reflectionClass->getMethod($method)->getParameters() as $index => $parameter) {
                if ($parameter->getType() != null && ApplicationInterface::class === $parameter->getType()->getName()) {
                    if (false === (bool) ($controllerArguments[$index] instanceof ApplicationInterface)) {
                        $vendor = $request->get('vendor');
                        $package = $request->get('extension');
                        $name = $request->get('application');

                        $application = $this->packageManager->getApplicationByAttributes($vendor, $package, $name);

                        if (!empty($application)) {
                            $args[] = $application;

                            continue;
                        }
                    }
                }
                
                $args[] = $controllerArguments[$index];
            }

            $event->setArguments($args);
        }
    }
}
