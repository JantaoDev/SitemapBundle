<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use JantaoDev\SitemapBundle\Sitemap\SitemapInterface;

/**
 * Sitemap generate event
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class SitemapGenerateEvent extends Event
{
    
    const ON_SITEMAP_GENERATE = 'jantao_dev.sitemap.generate';
    
    /**
     * @var SitemapInterface
     */
    protected $sitemap;
    
    /**
     * @param SitemapInterface $sitemap Sitemap
     */
    public function __construct(SitemapInterface $sitemap)
    {
        $this->sitemap = $sitemap;
    }

    /**
     * Get sitemap
     * 
     * @return SitemapInterface
     */
    public function getSitemap(): SitemapInterface
    {
        return $this->sitemap;
    }
    
}
