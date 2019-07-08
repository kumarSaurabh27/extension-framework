<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('shopify')
            ->children()
                ->node('stores', 'scalar')->end()
            ->end();
        
        $treeBuilder->root('shopify')
            ->children()
                ->node('stores', 'array')
                    ->arrayPrototype()
                        ->children()
                            ->node('domain', 'scalar')->cannotBeEmpty()->end()
                            ->node('api_key', 'scalar')->cannotBeEmpty()->end()
                            ->node('api_password', 'scalar')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
