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
                            ->node('id', 'scalar')->cannotBeEmpty()->end()
                            ->node('domain', 'scalar')->cannotBeEmpty()->end()
                            ->node('name', 'scalar')->cannotBeEmpty()->end()
                            ->node('client', 'scalar')->cannotBeEmpty()->end()
                            ->node('password', 'scalar')->cannotBeEmpty()->end()
                            ->node('enabled', 'boolean')->defaultFalse()->end()
                            ->node('timezone', 'scalar')->cannotBeEmpty()->end()
                            ->node('iana_timezone', 'scalar')->cannotBeEmpty()->end()
                            ->node('currency_format', 'scalar')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
