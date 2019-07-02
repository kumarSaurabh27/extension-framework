<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Package;

interface ExecutablePackageInterface
{
    public function __construct(Package $package);

    public function install();
}