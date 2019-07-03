<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition;

use Symfony\Component\Config\Definition\ConfigurationInterface;

abstract class Module implements ModuleInterface
{
    CONST EXTENSION_TYPE = 'uvdesk-module';

    public function getServices()
    {
        return [];
    }

    public function getApplications()
    {
        return [];
    }

    public function getConfiguration() : ?ConfigurationInterface
    {
        return null;
    }
}
