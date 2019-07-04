<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition;

abstract class ConfigurablePackage extends Package implements ConfigurablePackageInterface
{
    public static $root = '';

    public static function install(PackageMetadata $metadata) : void
    {
        return;
    }

    public static function updatePackageConfiguration(PackageMetadata $metadata, string $content) : void
    {
        if (empty($content)) {
            throw new \Exception('Configuration file cannot be empty');
        }

        $path = self::$root . "/" . str_replace('/', '_', $metadata->getName()) . ".yaml";

        if (!file_exists($path) || is_dir($path)) {
            file_put_contents($path, $content);
        }

        return;
    }
}