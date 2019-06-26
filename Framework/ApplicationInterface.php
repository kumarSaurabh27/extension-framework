<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Framework;

use Webkul\UVDesk\ExtensionFrameworkBundle\Module\ModuleInterface;

interface ApplicationInterface
{
    public function setExtension(ModuleInterface $extension) : ApplicationInterface;

    public function getExtension() : ModuleInterface;

    public function setExtensionReference($extensionReference) : ApplicationInterface;

    public function getExtensionReference() : string;
    
    public static function getIcon() : string;

    public static function getName() : string;

    public static function getSummary() : string;

    public static function getDescription() : string;

    public static function getQualifiedName() : string;
}
