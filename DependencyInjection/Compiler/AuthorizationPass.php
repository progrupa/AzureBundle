<?php

namespace Progrupa\AzureBundle\DependencyInjection\Compiler;

class AuthorizationPass implements \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    public function process(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        if (! $container->hasParameter('progrupa.azure.authentication')) {
            throw new \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException('Authentication scheme for Azure not configured');
        }

        if ('shared_key' == $container->getParameter('progrupa.azure.authentication')) {
            $def = $container->findDefinition('progrupa.azure.authentication.plugin');
            $def->setClass($container->getParameter('progrupa.azure.shared_key_auth.class'));
            $def->setArguments([
                $container->getParameter('progrupa.azure.account_name'),
                $container->getParameter('progrupa.azure.account_key'),
            ]);
        } elseif ('oauth2' == $container->getParameter('progrupa.azure.authentication')) {
            $def = $container->findDefinition('progrupa.azure.authentication.plugin');
            $def->setClass($container->getParameter('progrupa.azure.oauth2_auth.class'));
            $def->setArguments([
                $container->getParameter('progrupa.azure.active_directory_id'),
                $container->getParameter('progrupa.azure.application_id'),
                $container->getParameter('progrupa.azure.application_key'),
                $container->getParameter('progrupa.azure.application_redirect_url'),
                $container->getParameter('progrupa.azure.application_id_uri'),
            ]);

            $http = $container->findDefinition('progrupa.azure.http');
            $config = $http->getArgument(0);
            $config['auth'] = 'oauth2';
            $http->setArguments([$config]);
        }
    }
}
