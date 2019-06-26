<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Framework;

use Webkul\UVDesk\ExtensionFrameworkBundle\Framework\ModuleInterface;

abstract class Application implements ApplicationInterface
{
    CONST SVG = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="60px" height="60px" viewBox="0 0 60 60">
    <path fill-rule="evenodd" d="M17,26a4,4,0,1,1-4,4A4,4,0,0,1,17,26Zm13,0a4,4,0,1,1-4,4A4,4,0,0,1,30,26Zm13,0a4,4,0,1,1-4,4A4,4,0,0,1,43,26Z"></path>
</svg>
SVG;

    protected $listeners;
    protected $extension;
    protected $extensionReference;

    final public function setExtension(ModuleInterface $extension) : ApplicationInterface
    {
        if (null == $this->extension) {
            $this->extension = $extension;
        }

        return $this;
    }

    final public function getExtension() : ModuleInterface
    {
        return $this->extension;
    }

    final public function setExtensionReference($extensionReference) : ApplicationInterface
    {
        if (null == $this->extensionReference) {
            $this->extensionReference = $extensionReference;
        }

        return $this;
    }

    final public function getExtensionReference() : string
    {
        return $this->extensionReference;
    }

    public static function getIcon() : string
    {
        return self::SVG;
    }

    public abstract static function getName() : string;

    public abstract static function getSummary() : string;

    public abstract static function getDescription() : string;

    public abstract static function getQualifiedName() : string;
}
