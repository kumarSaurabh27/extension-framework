<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition;

abstract class Package implements PackageInterface
{
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
}