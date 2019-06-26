<?php

namespace UVDesk\CommunityExtension\UVDesk\Commons\Apps;

use Webkul\UVDesk\ExtensionBundle\Framework\Application;

class CustomerNotes extends Application
{
    public static function getName() : string
    {
        return "Customer Notes";
    }

    public static function getSummary() : string
    {
        return "Add important notes to support tickets accessible to all agents";
    }

    public static function getDescription() : string
    {
        return "Write notes pertaining to customers which will be visible to all agents across your helpdesk. This provides an effective way of storing important details which may be vital in providing a better support to your customers.";
    }

    public static function getQualifiedName() : string
    {
        return "customer-notes";
    }
}
