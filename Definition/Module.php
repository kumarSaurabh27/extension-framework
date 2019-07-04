<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition;

use Symfony\Component\Config\Definition\ConfigurationInterface;

abstract class Module implements ModuleInterface
{
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
