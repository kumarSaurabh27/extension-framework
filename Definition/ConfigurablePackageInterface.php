<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition;

interface ConfigurablePackageInterface extends PackageInterface
{
    public function install();
}