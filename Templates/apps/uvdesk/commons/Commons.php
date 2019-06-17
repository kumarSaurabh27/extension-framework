<?php

namespace UVDeskApps\UVDesk\Commons;

use Webkul\UVDesk\ExtensionBundle\Apps\Application;

class Commons extends Application
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
