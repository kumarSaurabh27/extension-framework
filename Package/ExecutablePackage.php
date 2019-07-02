<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Package;

abstract class ExecutablePackage implements ExecutablePackageInterface
{
    public static $directory;

    final public function __construct(Package $package)
	{
		$this->package = $package;
	}

    final protected function copyConfiguration($src, $env = 'all')
    {
        if (!file_exists($src) || is_dir($src)) {
            throw new \Exception("File '$src' nout found");
        }

        $content = file_get_contents($src);
        $name = str_replace('/', '_', $this->package->getName()) . ".yaml";
        $path = self::$directory . ((empty($env) || $env === 'all') ? "/" : "/$env/") . $name;

        if (!file_exists($path) || is_dir($path)) {
            file_put_contents($path, $content);
        }
    }

    public function install() {}
}