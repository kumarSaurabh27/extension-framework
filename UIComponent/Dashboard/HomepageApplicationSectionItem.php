<?php

namespace Webkul\UVDesk\ExtensionBundle\UIComponent\Dashboard;

use Webkul\UVDesk\CoreBundle\Extensions\Type\HomepageSectionItem;

abstract class HomepageApplicationSectionItem extends HomepageSectionItem
{
    public abstract static function getTitle() : string;
    public abstract static function getRouteName() : string;

    public static function getSectionReferenceId() : string
    {
        return HomepageApplicationSection::class;
    }
}
