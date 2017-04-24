<?php

namespace Progrupa\AzureBundle;

use Progrupa\AzureBundle\DependencyInjection\Compiler\AuthorizationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ProgrupaAzureBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AuthorizationPass());
    }
}
