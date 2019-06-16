<?php

namespace Webkul\UVDesk\ExtensionBundle\Package;

use Webkul\UVDesk\PackageManager\Composer\ComposerPackage;
use Webkul\UVDesk\PackageManager\Composer\ComposerPackageExtension;

class Composer extends ComposerPackageExtension
{
    public function loadConfiguration()
    {
        $composerPackage = new ComposerPackage();
        $composerPackage
            ->movePackageConfig('config/packages/uvdesk_extensions.yaml', 'Templates/config.yaml')
            ->movePackageConfig('config/routes/uvdesk_extensions.yaml', 'Templates/routes.yaml');
        
        return $composerPackage;
    }
}
