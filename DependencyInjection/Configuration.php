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
        $rootNode = $treeBuilder->root('atipik_hoa_web_socket');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('listen_address')->end()
                ->scalarNode('listen_port')->end()
                ->scalarNode('address')->end()
                ->scalarNode('port')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
