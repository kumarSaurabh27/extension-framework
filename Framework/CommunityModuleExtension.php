<?php

namespace Webkul\UVDesk\ExtensionBundle\Framework;

abstract class CommunityModuleExtension implements CommunityModuleExtensionInterface
{
    public static function getServices() : array
    {
        return [];
    }

    public static function getApplications() : array
    {
        return [];
    }
}
