<?php

namespace Webkul\UVDesk\ExtensionBundle\Framework;

interface HelpdeskModuleInterface
{
    public static function env() : array;
    public static function services() : array;
    public static function getTitle() : string;
    public static function getDescription() : string;
}
