<?php

namespace Atipik\Hoa\WebSocketBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Hoa Extension
 *
 * Load hoa prefix
 */
class HoaExtension extends Extension
{
    /**
     * Load configuration
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->loadDefaultParameters($container);
        $this->loadParameters($container, $config, 'hoa.');

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Load default parameters
     *
     * @param ContainerBuilder $container
     */
    public function loadDefaultParameters(ContainerBuilder $container)
    {
        // Server
        if (!$container->hasParameter('hoa.websocket.runner.class')) {
            $container->setParameter(
                'hoa.websocket.runner.class',
                'Atipik\Hoa\WebSocketBundle\WebSocket\Runner'
            );
        }

        if (!$container->hasParameter('hoa.websocket.server.class')) {
            $container->setParameter(
                'hoa.websocket.server.class',
                'Atipik\Hoa\WebSocketBundle\WebSocket\Server'
            );
        }

        if (!$container->hasParameter('hoa.socket.server.class')) {
            $container->setParameter(
                'hoa.socket.server.class',
                'Hoa\Socket\Server'
            );
        }

        if (!$container->hasParameter('hoa.websocket.node.class')) {
            $container->setParameter('hoa.websocket.node.class', null);
        }

        if (!$container->hasParameter('hoa.websocket.logger.class')) {
            $container->setParameter(
                'hoa.websocket.logger.class',
                'Atipik\Hoa\WebSocketBundle\Log\Logger'
            );
        }

        // Client
        if (!$container->hasParameter('hoa.websocket.client.class')) {
            $container->setParameter(
                'hoa.websocket.client.class',
                'Hoa\Websocket\Client'
            );
        }

        if (!$container->hasParameter('hoa.socket.client.class')) {
            $container->setParameter(
                'hoa.socket.client.class',
                'Hoa\Socket\Client'
            );
        }
    }

    /**
     * Load parameters
     *
     * @param ContainerBuilder $container
     * @param array            $config
     * @param string           $prefix
     */
    public function loadParameters(ContainerBuilder $container, array $config, $prefix = '')
    {
        foreach ($config as $name => $value) {
            $prefixedName = $prefix . $name;

            if (is_array($value)) {
                $this->loadParameters($container, $value, $prefixedName . '.');
            } else {
                $container->setParameter($prefixedName, $value);
            }
        }
    }
}
