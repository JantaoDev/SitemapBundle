<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jantao_dev_sitemap');
        
        $rootNode
            /*->fixXmlConfig('allow')
            ->fixXmlConfig('disallow')
            ->fixXmlConfig('clean_param')*/
            ->children()
                ->arrayNode('hosts')
                    ->prototype('scalar')->end()
                ->end()
                ->enumNode('scheme')
                    ->treatNullLike('http')
                    ->treatFalseLike('http')
                    ->treatTrueLike('https')
                    ->defaultValue(null)
                    ->values(['http', 'https', 'https_only'])
                ->end()
                ->scalarNode('port')
                    ->defaultNull()
                ->end()
                ->scalarNode('web_dir')
                    ->defaultValue('%kernel.root_dir%/../web')
                ->end()
                ->booleanNode('gzip')
                    ->defaultFalse()
                ->end()
                ->arrayNode('robots')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('allow')
                            ->useAttributeAsKey('path')
                            ->prototype('scalar')
                                ->beforeNormalization()
                                    ->always(function ($v) {return (is_string($v) && $v) ? $v : '*';})
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('disallow')
                            ->useAttributeAsKey('path')
                            ->prototype('scalar')
                                ->beforeNormalization()
                                    ->always(function ($v) {return (is_string($v) && $v) ? $v : '*';})
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('crawl_delay')
                            ->defaultNull()
                        ->end()
                        ->arrayNode('clean_param')
                            ->useAttributeAsKey('path')
                            ->prototype('array')
                                ->beforeNormalization()
                                    ->ifTrue(function ($v) {return !isset($v['parameters']);})
                                    ->then(function ($v) {return ['parameters' => $v];})
                                ->end()
                                ->children()
                                    ->arrayNode('parameters')
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('sitemap')
                    ->useAttributeAsKey('route')
                    ->prototype('array')
                        ->canBeEnabled()
                        ->children()
                            ->booleanNode('enabled')->end()
                            ->scalarNode('last_mod')->end()
                            ->scalarNode('change_freq')->end()
                            ->scalarNode('priority')->end()
                            ->enumNode('iterator')->defaultValue(null)->values(['doctrine', 'array'])->end()
                            ->scalarNode('query')->end()
                            ->arrayNode('values')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('route_parameters')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        
        return $treeBuilder;
    }
}
