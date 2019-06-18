<?php

namespace Webkul\UVDesk\ExtensionBundle\Framework;

abstract class CommunityApplication implements CommunityApplicationInterface
{
    CONST SVG = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="60px" height="60px" viewBox="0 0 60 60">
    <path fill-rule="evenodd" d="M17,26a4,4,0,1,1-4,4A4,4,0,0,1,17,26Zm13,0a4,4,0,1,1-4,4A4,4,0,0,1,30,26Zm13,0a4,4,0,1,1-4,4A4,4,0,0,1,43,26Z"></path>
</svg>
SVG;

    private static $vendor;
    private static $extension;

    public static function getIcon() : string
    {
        return self::SVG;
    }

    final public static function setVendor($vendor) : void
    {
        self::$vendor = $vendor;
    }

    final public static function getVendor() : string
    {
        return self::$vendor;
    }

    final public static function setExtension($extension) : void
    {
        self::$extension = $extension;
    }

    final public static function getExtension() : string
    {
        return self::$extension;
    }

    public abstract static function getName() : string;

    public abstract static function getSummary() : string;

    public abstract static function getDescription() : string;

    public abstract static function getFullyQualifiedName() : string;
}
