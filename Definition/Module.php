<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition;

use Symfony\Component\Config\Definition\ConfigurationInterface;

abstract class Module implements ModuleInterface
{
    CONST EXTENSION_TYPE = 'uvdesk-module';

    final public function __construct()
    {
        return;
    }

    public function getConfiguration() : ?ConfigurationInterface
    {
        return null;
    }

    public function getServices()
    {
        return array();
    }

    public abstract function getPackageReference() : string;

    public function getApplicationReferences() : array
    {
        return array();
    }
}
