<?php

namespace Webkul\UVDesk\ExtensionBundle\Package;

use Webkul\UVDesk\PackageManager\Extensions\HelpdeskExtension;
use Webkul\UVDesk\PackageManager\ExtensionOptions\HelpdeskExtension\Section as HelpdeskSection;

class UvdeskExtensionsConfiguration extends HelpdeskExtension
{
    const MAILBOX_BRICK_SVG = <<<SVG
<path fill-rule="evenodd" d="M17,26a4,4,0,1,1-4,4A4,4,0,0,1,17,26Zm13,0a4,4,0,1,1-4,4A4,4,0,0,1,30,26Zm13,0a4,4,0,1,1-4,4A4,4,0,0,1,43,26Z" />
SVG;

    public function loadDashboardItems()
    {
        return [
            HelpdeskSection::APPS => [
                [
                    'name' => 'Explore Apps',
                    'route' => 'helpdesk_member_extensions',
                    'brick_svg' => self::MAILBOX_BRICK_SVG,
                    'permission' => 'ROLE_AGENT_MANAGE_WORKFLOW_AUTOMATIC',
                ],
            ],
        ];
    }

    public function loadNavigationItems()
    {
        return [];
    }
}
