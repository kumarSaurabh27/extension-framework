<?php

namespace UVDesk\CommunityPackages\UVDesk\Commons\Apps;

use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\Application;

class Memo extends Application
{
    public static function getName() : string
    {
        return "Memo";
    }

    public static function getSummary() : string
    {
        return "Add important memos to support tickets accessible to all agents";
    }

    public static function getDescription() : string
    {
        return "Write memos pertaining to customers which will be visible to all agents across your helpdesk.";
    }

    public static function getQualifiedName() : string
    {
        return "memo";
    }
}
