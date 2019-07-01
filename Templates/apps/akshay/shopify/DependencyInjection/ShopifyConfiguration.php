<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ShopifyConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('uvdesk_extension_akshay_shopify')
            ->children()
                ->node('something', 'scalar')->end()
            ->end();

        return $treeBuilder;
    }
}
