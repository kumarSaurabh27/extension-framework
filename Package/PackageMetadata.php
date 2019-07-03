<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Package;

class PackageMetadata
{
    private $source = '';
    private static $supportedTypes = ['uvdesk-module'];
    
    public function __construct($path = '')
    {
        if (!empty($path)) {
            if (!file_exists($path) || is_dir($path)) {
                throw new \Exception("Unable to initialize package. File '$path' does not exists.");
            }

            $this->source = dirname($path);

            foreach (json_decode(file_get_contents($path), true) as $attribute => $value) {
                switch ($attribute) {
                    case 'name':
                        $this->setName($value);
                        break;
                    case 'description':
                        $this->setDescription($value);
                        break;
                    case 'type':
                        $this->setType($value);
                        break;
                    case 'authors':
                        // $this->setName($value);
                        break;
                    case 'autoload':
                        $this->setDefinedNamespaces($value);
                        break;
                    case 'extensions':
                        foreach ($value as $extensionReference => $env) {
                            $this->addExtensionReference($extensionReference, $env);
                        }

                        break;
                    case 'scripts':
                        foreach ($value as $script) {
                            $this->addScript($script);
                        }

                        break;
                    default:
                        break;
                }
            }
        }
    }

    public function getRootDirectory()
    {
        return $this->source;
    }

    public function setName(string $name) : Package
    {
        list($vendor, $package) = explode('/', $name);

        $this->name = $name;
        $this->vendor = $vendor;
        $this->package = $package;

        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getVendor() : string
    {
        return $this->vendor;
    }

    public function getPackage() : string
    {
        return $this->package;
    }

    public function setDescription(string $description) : Package
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function setType(string $type) : Package
    {
        if (!in_array($type, self::$supportedTypes)) {
            throw new \Exception("Invalid package type " . $type . ". Supported types are [" . implode(", ", self::$supportedTypes) . "]");
        }

        $this->type = $type;

        return $this;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function setDefinedNamespaces(array $definedNamespaces)
    {
        $this->definedNamespaces = $definedNamespaces;

        return $this;
    }

    public function getDefinedNamespaces() : array
    {
        return $this->definedNamespaces;
    }

    public function addExtensionReference($extensionReference, $env)
    {
        $this->extensionReference[$extensionReference] = $env;

        return $this;
    }

    public function getExtensionReferences()
    {
        return $this->extensionReference;
    }

    public function addScript($script)
    {
        $this->scripts[] = $script;

        return $this;
    }

    public function getScripts()
    {
        return $this->scripts;
    }
}