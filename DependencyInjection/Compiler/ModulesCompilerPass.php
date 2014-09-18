<?php

namespace Atipik\Hoa\WebSocketBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Auto-inject modules in server
 */
class ModulesCompilerPass implements CompilerPassInterface
{
    /**
     * Process
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('atipik_hoa_web_socket.runner')
        ||  !$container->hasDefinition('atipik_hoa_web_socket.logger')
        ||  !$container->hasDefinition('service_container')) {
            return;
        }

        $runner  = $container->getDefinition('atipik_hoa_web_socket.runner');
        $modules = $container->findTaggedServiceIds('atipik_hoa_web_socket.module');

        foreach ($modules as $moduleServiceId => $attributes) {
            $module = $container->getDefinition($moduleServiceId);

            // inject logger
            $module->addMethodCall(
                'setLogger',
                array(
                    new Reference('atipik_hoa_web_socket.logger')
                )
            );

            // link module
            $runner->addMethodCall(
                'addModule',
                array(
                    new Reference($moduleServiceId)
                )
            );
        }
    }
}
