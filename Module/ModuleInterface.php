<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Module;

use Webkul\UVDesk\ExtensionFrameworkBundle\Package\Package;

interface ModuleInterface
{
    public function getPackage() : Package;

    public static function getServices() : array;

    public static function getApplications() : array;
}
