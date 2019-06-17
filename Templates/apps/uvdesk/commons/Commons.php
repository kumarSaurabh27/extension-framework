<?php

namespace UVDeskApps\UVDesk\Commons;

use Webkul\UVDesk\ExtensionBundle\Framework\HelpdeskModule;

class Commons extends HelpdeskModule
{
    public static function getTitle() : string
    {
        return "UVDesk Commons";
    }

    public static function getDescription() : string
    {
        return "Common helpdesk utilities";
    }

    public static function services() : array
    {
        return [
            '/Resources/config/services.yaml'
        ];
    }
}
