<?php

namespace UVDesk\CommunityPackages\UVDesk\Commons;

use UVDesk\CommunityPackages\UVDesk\Commons\Apps;
use Webkul\UVDesk\ExtensionFrameworkBundle\Module\Module;

final class Commons extends Module
{
    public static function getServices() : array
    {
        return [
            __DIR__ . "/Resources/config/services.yaml"
        ];
    }

    public static function getApplications() : array
    {
        return [
            Apps\CustomerNotes::class,
            Apps\Memo::class,
        ];
    }
}
