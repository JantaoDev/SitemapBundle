<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Service;

use JantaoDev\SitemapBundle\Event\SitemapGenerateEvent;

/**
 * Event listener interface
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
interface SitemapListenerInterface
{
    
    /**
     * Generate sitemap
     * 
     * @param SitemapGenerateEvent $event
     */
    public function generateSitemap(SitemapGenerateEvent $event);
    
}
