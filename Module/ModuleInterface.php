<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Framework;

interface ModuleInterface
{
    public function getVendor() : string;

    public function getPackage() : string;

    public function getDirectory() : string;

    public static function getServices() : array;

    public static function getApplications() : array;
}
