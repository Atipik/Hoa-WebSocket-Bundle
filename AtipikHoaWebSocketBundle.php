<?php

namespace Atipik\Hoa\WebSocketBundle;

use Atipik\Hoa\WebSocketBundle\DependencyInjection\HoaExtension;
use Atipik\Hoa\WebSocketBundle\DependencyInjection\Compiler\ModulesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle class
 */
class AtipikHoaWebSocketBundle extends Bundle
{
    /**
     * Build bundle
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ModulesCompilerPass());
    }
}
