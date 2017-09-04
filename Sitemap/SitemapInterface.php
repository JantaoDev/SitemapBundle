<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Sitemap;

use JantaoDev\SitemapBundle\Sitemap\UrlInterface;

/**
 * Sitemap interface
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
interface SitemapInterface
{
    
    /**
     * Get robots.txt file object
     * 
     * @return RobotsFileInterface
     */
    public function getRobotsFile();
    
    /**
     * Get host object
     * 
     * @return HostInterface
     */
    public function getHost();
    
    /**
     * Add URL
     * 
     * @param UrlInterface $url
     */
    public function add(UrlInterface $url);
    
    /**
     * Save sitemap
     * 
     * @param string $folder Web folder
     * @param bool $gzip Enabled gzip compression
     * @param string $postfix File postfix if several hosts are configured
     */
    public function save($folder, $gzip = false, $postfix = null);
    
}
