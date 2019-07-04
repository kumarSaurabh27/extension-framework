<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition;

use Symfony\Component\Config\Definition\ConfigurationInterface;

interface ModuleInterface
{
    public function getConfiguration() : ?ConfigurationInterface;

    public function getServices();

    public function getPackageReference() : string;

    public function getApplicationReferences() : array;
}
