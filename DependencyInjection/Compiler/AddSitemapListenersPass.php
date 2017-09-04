<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use JantaoDev\SitemapBundle\Event\SitemapGenerateEvent;

/**
 * Registering services tagged with jantao_dev.sitemap.listener as sitemap event listeners
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class AddSitemapListenersPass implements CompilerPassInterface
{
    
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('event_dispatcher') && !$container->hasAlias('event_dispatcher')) {
            return;
        }
        $eventDispatcher = $container->findDefinition('event_dispatcher');
        foreach ($container->findTaggedServiceIds('jantao_dev.sitemap.listener') as $id => $tags) {
            $class = $container->getDefinition($id)->getClass();
            $reflectionClass = new \ReflectionClass($class);
            if (!$reflectionClass->implementsInterface('JantaoDev\SitemapBundle\Service\SitemapListenerInterface')) {
                throw new \InvalidArgumentException("Service $id must implement interface JantaoDev\SitemapBundle\Service\SitemapListenerInterface");
            }
            $eventDispatcher->addMethodCall('addListenerService', [SitemapGenerateEvent::ON_SITEMAP_GENERATE, [$id, 'generateSitemap']]);
        }
    }
    
}