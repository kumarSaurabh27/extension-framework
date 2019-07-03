<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Package;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ConfigurablePackageInterface;

class ShopifyPackage extends Package implements ConfigurablePackageInterface
{
    public function install()
    {
        $this->copy(__DIR__ . "/../templates/config.yaml");
    }

    private function copy($src, $env = 'all')
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
}
