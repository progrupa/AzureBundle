<?php

namespace Progrupa\AzureBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ProgrupaAzureExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('progrupa.azure.account_url', $config['account_url']);
        $container->setParameter('progrupa.azure.api_endpoint', sprintf("https://%s/", $config['account_url']));

        if (array_key_exists('shared_key', $config)) {
            $container->setParameter('progrupa.azure.authentication', 'shared_key');
            $container->setParameter('progrupa.azure.account_name', $config['shared_key']['account_name']);
            $container->setParameter('progrupa.azure.account_key', $config['shared_key']['account_key']);
        } elseif (array_key_exists('oauth2', $config)) {
            $container->setParameter('progrupa.azure.authentication', 'oauth2');
            $container->setParameter('progrupa.azure.active_directory_id', $config['oauth2']['active_directory_id']);
            $container->setParameter('progrupa.azure.application_id', $config['oauth2']['application_id']);
            $container->setParameter('progrupa.azure.application_key', $config['oauth2']['application_key']);
            $container->setParameter('progrupa.azure.application_redirect_url', $config['oauth2']['application_redirect_url']);
            $container->setParameter('progrupa.azure.application_id_uri', $config['oauth2']['application_id_uri']);
        } else {
            throw new InvalidConfigurationException("ProgrupaAzureExtension requires either 'shared_key' or 'oauth2' option defined");
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
