<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition;

interface ConfigurablePackageInterface extends PackageInterface
{
    public static function install(PackageMetadata $metadata);
}