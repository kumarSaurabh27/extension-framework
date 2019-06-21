<?php

namespace Webkul\UVDesk\ExtensionBundle\Routes;

use Webkul\UVDesk\CoreBundle\Routing\CoreRoutingInterface;

class ExtensionRoutes implements CoreRoutingInterface
{
    public static function getResourcePath()
    {
        return __DIR__ . "/_routes/extension.yaml";
    }

    public static function getResourceType()
    {
        return RouterInterface::YAML_RESOURCE;
    }
}
