<?php

namespace Webkul\UVDesk\ExtensionBundle\Framework;

interface CommunityApplicationInterface
{
    public static function getIcon() : string;

    public static function setVendor($vendor) : void;

    public static function getVendor() : string;

    public static function setExtension($extension) : void;

    public static function getExtension() : string;

    public static function getName() : string;

    public static function getSummary() : string;

    public static function getDescription() : string;

    public static function getFullyQualifiedName() : string;
}
