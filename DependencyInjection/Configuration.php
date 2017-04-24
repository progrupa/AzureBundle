<?php

namespace Progrupa\AzureBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('progrupa_azure')
            ->children()
                ->scalarNode('account_url')->isRequired(true)->end()
                ->arrayNode('shared_key')->canBeUnset()
                    ->children()
                        ->scalarNode('account_name')->isRequired(true)->end()
                        ->scalarNode('account_key')->isRequired(true)->end()
                    ->end()
                ->end()
                ->arrayNode('oauth2')->canBeUnset()
                    ->children()
                        ->scalarNode('active_directory_id')->isRequired(true)->end()
                        ->scalarNode('application_id')->isRequired(true)->end()
                        ->scalarNode('application_key')->isRequired(true)->end()
                        ->scalarNode('application_redirect_url')->isRequired(true)->end()
                        ->scalarNode('application_id_uri')->isRequired(true)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
