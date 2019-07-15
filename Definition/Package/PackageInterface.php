<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package;

use Symfony\Component\Config\Definition\ConfigurationInterface;

interface PackageInterface
{
    public function getConfiguration() : ?ConfigurationInterface;
}
