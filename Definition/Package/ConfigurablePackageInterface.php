<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package;

interface ConfigurablePackageInterface extends PackageInterface
{
    public function install();
}