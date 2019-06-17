<?php

namespace Webkul\UVDesk\ExtensionBundle\Routes;

use Webkul\UVDesk\CoreBundle\Routing\RouterInterface;

class ExtensionRoutes implements RouterInterface
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
