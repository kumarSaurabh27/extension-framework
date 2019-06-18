<?php

namespace UVDeskApps\UVDesk\Commons;

use UVDeskApps\UVDesk\Commons\Apps\Memo;
use UVDeskApps\UVDesk\Commons\Apps\CustomerNotes;
use Webkul\UVDesk\ExtensionBundle\Framework\CommunityModuleExtension;

final class Commons extends CommunityModuleExtension
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
