<?php

namespace UVDeskApps\UVDesk\Commons\Apps;

use Webkul\UVDesk\ExtensionBundle\Extensions\Type\ApplicationInterface;

class Memo implements ApplicationInterface
{
    CONST SVG = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="18px">
    <path fill-rule="evenodd" fill="rgb(51, 51, 51)" d="M19.000,-0.000 L1.000,-0.000 L1.000,2.000 L19.000,2.000 L19.000,-0.000 ZM20.000,11.000 L20.000,8.995 L19.000,3.000 L1.000,3.000 L0.000,9.000 L0.000,11.000 L1.000,11.000 L1.000,18.000 L12.000,18.000 L12.000,11.000 L17.000,11.000 L17.000,18.000 L19.000,18.000 L19.000,11.000 L20.000,11.000 ZM10.000,16.000 L3.000,16.000 L3.000,11.000 L10.000,11.000 L10.000,16.000 Z"/>
</svg>
SVG;

    public static function getIcon() : string
    {
        return self::SVG;
    }

    public static function getTitle() : string
    {
        return "Memo";
    }

    public static function getDescription() : string
    {
        return "Memo";
    }
}
