<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\DependencyInjection;

use JantaoDev\SitemapBundle\EventListener\ConfigSitemapListener;
use JantaoDev\SitemapBundle\Service\SitemapService;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('jantao_dev_sitemap.hosts', $config['hosts']);
        $container->setParameter('jantao_dev_sitemap.web_dir', $config['web_dir']);

        $def = $container->getDefinition(SitemapService::class);
        $def
            ->setArguments([
                '$hosts' => $config['hosts'],
                '$scheme' => $config['scheme'],
                '$port' => $config['port'],
                '$webDir' => $config['web_dir'],
                '$gzip' => $config['gzip'],
                '$robots' => $config['robots'],
            ]);

        $def = $container->getDefinition(ConfigSitemapListener::class);
        $def->setArgument('$sitemap', $config['sitemap']);
    }
}
