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

        $this->loadParameters($container, $config, 'hoa.');

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
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
