<?php

namespace Webkul\UVDesk\ExtensionBundle\Framework;

use Webkul\UVDesk\ExtensionBundle\Framework\CommunityModuleExtensionInterface;

interface CommunityApplicationInterface
{
    public function getExtension() : CommunityModuleExtensionInterface;
    
    public static function getIcon() : string;

    public static function getName() : string;

    public static function getSummary() : string;

    public static function getDescription() : string;

    public static function getQualifiedName() : string;
}
