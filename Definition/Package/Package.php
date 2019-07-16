<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package;

use Symfony\Component\Config\Definition\ConfigurationInterface;

abstract class Package implements PackageInterface
{
    protected $configs;
    protected $metadata;

    final public function setMetadata(PackageMetadata $metadata) : PackageInterface
	{
        $this->metadata = $metadata;

        return $this;
    }
    
    final public function getMetadata() : PackageMetadata
    {
        return $this->metadata;
    }

    final public function setConfigurations(array $configs = [])  : PackageInterface
    {
        $this->configs = $configs;

        return $this;
    }

    final public function getConfigurations() : array
    {
        return $this->configs;
    }

    public function getConfiguration() : ?ConfigurationInterface
    {
        return null;
    }
}