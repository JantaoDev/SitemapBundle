<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Sitemap;
use JantaoDev\SitemapBundle\Sitemap\HostInterface;

/**
 * Url interface
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
interface UrlInterface
{
    
    /**
     * Get XML representation
     * 
     * @param HostInterface $host
     */
    public function getXML(HostInterface $host);
    
}
