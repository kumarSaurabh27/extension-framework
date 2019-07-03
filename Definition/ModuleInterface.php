<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition;

use Symfony\Component\Config\Definition\ConfigurationInterface;

interface ModuleInterface
{
    public function getServices();

    public function getApplications();

    public function getConfiguration() : ?ConfigurationInterface;
}
