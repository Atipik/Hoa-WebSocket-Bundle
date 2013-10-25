<?php

namespace Atipik\Hoa\WebSocketBundle\DependencyInjection;

use Atipik\Hoa\WebSocketBundle\WebSocket\Runner;
use Atipik\Hoa\WebSocketBundle\WebSocket\MessageImprovedServer;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hoa');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('websocket')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('address')
                            ->defaultValue(Runner::DEFAULT_ADDRESS)
                        ->end()
                        ->scalarNode('port')
                            ->defaultValue(Runner::DEFAULT_PORT)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
