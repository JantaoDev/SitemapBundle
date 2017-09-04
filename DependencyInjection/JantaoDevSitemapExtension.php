<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class JantaoDevSitemapExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        
        $container->setParameter('jantao_dev_sitemap.hosts', $config['hosts']);
        $container->setParameter('jantao_dev_sitemap.web_dir', $config['web_dir']);
        
        $def = $container->getDefinition('jantao_dev.sitemap');
        $def->replaceArgument(2, $config['hosts']);
        $def->replaceArgument(3, $config['scheme']);
        $def->replaceArgument(4, $config['port']);
        $def->replaceArgument(5, $config['web_dir']);
        $def->replaceArgument(6, $config['gzip']);
        $def->replaceArgument(7, $config['robots']);
        $def = $container->getDefinition('jantao_dev.sitemap.config_sitemap_listener');
        $def->replaceArgument(2, $config['sitemap']);
    }
}
