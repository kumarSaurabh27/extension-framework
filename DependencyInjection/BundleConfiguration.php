<?php

namespace Webkul\UVDesk\ExtensionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class BundleConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('uvdesk_extensions')
            ->children()
                ->node('dir', 'scalar')->defaultValue('%kernel.project_dir%/apps')->end()
            ->end();

        return $treeBuilder;
    }
}
