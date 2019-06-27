<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Package;

use Webkul\UVDesk\ExtensionFrameworkBundle\Module\ModuleInterface;

class Package
{
    private $name;
    private $source;
    private $vendor;
    private $package;
    private $description;
    private $packageType;
    private $definedNamespaces = [];
    private $extension;
    private static $supportedTypes = ['uvdesk-module'];
    
    private function setName(string $name) : Package
    {
        $this->name = $name;

        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    private function setSource(string $source) : Package
    {
        $this->source = $source;

        return $this;
    }

    public function getSource() : string
    {
        return $this->source;
    }

    private function setVendor(string $vendor) : Package
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function getVendor() : string
    {
        return $this->vendor;
    }

    private function setPackage(string $package) : Package
    {
        $this->package = $package;

        return $this;
    }

    public function getPackage() : string
    {
        return $this->package;
    }

    private function setDescription(string $description) : Package
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    private function setPackageType(string $packageType) : Package
    {
        $this->packageType = $packageType;

        return $this;
    }

    public function getPackageType() : string
    {
        return $this->packageType;
    }

    private function setDefinedNamespaces(array $definedNamespaces)
    {
        $this->definedNamespaces = $definedNamespaces;

        return $this;
    }

    public function getDefinedNamespaces() : array
    {
        return $this->definedNamespaces;
    }

    public function isValid() : bool
    {
        if (in_array($this->getPackageType(), self::$supportedTypes) && $this->getName() != null) {
            return true;
        }

        return false;
    }

    private function searchPackageExtensionClassIteratively() : ?\ReflectionClass
    {
        foreach ($this->getDefinedNamespaces() as $namespace => $relativePath) {
            $path = $this->getSource() . "/" . $relativePath;

            foreach (array_diff(scandir($path), ['.', '..']) as $item) {
                $resource = "$path$item";

                if (is_file($resource) && !is_dir($resource) && 'php' === pathinfo($resource, PATHINFO_EXTENSION)) {
                    $className = $namespace . pathinfo($resource, PATHINFO_FILENAME);
                    
                    try {
                        include_once $resource;
                        $reflectionClass = new \ReflectionClass($className);
                    } catch (\Exception $e) {
                        continue;
                    }

                    switch ($this->getPackageType()) {
                        case 'uvdesk-module':
                            if ($reflectionClass->implementsInterface(ModuleInterface::class)) {
                                return $reflectionClass;
                            }
                            
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        return null;
    }

    public function getExtension() : ?\ReflectionClass
    {
        if (empty($this->extension)) {
            $this->extension = $this->searchPackageExtensionClassIteratively();
        }
        
        return $this->extension;
    }

    public static function createFromAttributes($vendor, $package, $source) : Package
    {
        if (!file_exists($source) || is_dir($source)) {
            throw new \Exception("Unable to initialize package. File '$source' does not exists.");
        } else {
            $attributes = json_decode(file_get_contents($source), true);
    
            if ("$vendor/$package" != $attributes['name']) {
                throw new \Exception("Invalid package extension.json file. The qualified package name should be '$vendor/$package' but the specified name is '" . $attributes['name'] . "' in '$source'");
            }
        }

        return (new Package())
            ->setName($attributes['name'])
            ->setVendor($vendor)
            ->setPackage($package)
            ->setSource(dirname($source))
            ->setDescription($attributes['description'])
            ->setPackageType($attributes['type'])
            ->setDefinedNamespaces($attributes['autoload']['psr-4']);
    }
}