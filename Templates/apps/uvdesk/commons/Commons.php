<?php

namespace UVDesk\CommunityExtension\UVDesk\Commons;

use UVDesk\CommunityExtension\UVDesk\Commons\Apps\Memo;
use UVDesk\CommunityExtension\UVDesk\Commons\Apps\CustomerNotes;
use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\Module;

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
            CustomerNotes::class,
            Memo::class,
        ];
    }
}
