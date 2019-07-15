<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package;

abstract class ConfigurablePackage extends Package implements ConfigurablePackageInterface
{
    protected $pathToConfigurationFile;

    public function setPathToConfigurationFile(string $pathToConfigurationFile) : void
    {
        if (empty($this->pathToConfigurationFile)) {
            $this->pathToConfigurationFile = $pathToConfigurationFile;
        }
    }

    public function getPathToConfigurationFile() : string
    {
        return $this->pathToConfigurationFile;
    }

    public function install() : void
    {
        return;
    }

    public function updatePackageConfiguration(string $content) : void
    {
        if (empty($content)) {
            throw new \Exception('Configuration file cannot be empty');
        }

        file_put_contents($this->getPathToConfigurationFile(), $content);
    }
}