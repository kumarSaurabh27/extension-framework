<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('akshay_shopify')
            ->children()
                ->node('channels', 'scalar')->end()
            ->end();

        return $treeBuilder;
    }
}
