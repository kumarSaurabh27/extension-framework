<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ShopifyConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('uvdesk_extension_uvdesk_shopify')
            ->children()
                ->node('dir', 'scalar')->defaultValue('%kernel.project_dir%/apps')->end()
            ->end();

        return $treeBuilder;
    }
}
