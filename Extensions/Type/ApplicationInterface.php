<?php

namespace Webkul\UVDesk\ExtensionBundle\Extensions\Type;

interface ApplicationInterface
{
    public static function getIcon() : string;
    public static function getTitle() : string;
    public static function getDescription() : string;
}
