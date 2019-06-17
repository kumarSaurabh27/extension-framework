<?php

namespace UVDeskApps\UVDesk\ECommerce;

use Webkul\UVDesk\ExtensionBundle\Apps\Application;

class ECommerce extends Application
{
    public static function getTitle() : string
    {
        return "ECommerce";
    }

    public static function getDescription() : string
    {
        return "ECommerce utilities";
    }

    public static function services() : array
    {
        return [
            '/Resources/config/services.yaml'
        ];
    }
}
