<?php

namespace Webkul\UVDesk\ExtensionBundle\UIComponent\Dashboard;

use Webkul\UVDesk\CoreBundle\Extensions\Type\HomepageSection;

class HomepageApplicationSection extends HomepageSection
{
    public static function getTitle() : string
    {
        return "Apps";
    }

    public static function getDescription() : string
    {
        return "Integrate apps as per as your needs to get things done faster than ever";
    }

    public static function getRoles() : array
    {
        return [];
    }
}
