<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Sitemap;

/**
 * Robots.txt file interface
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
interface RobotsFileInterface
{
    
    /**
     * Add allow entry
     * 
     * @param string $path
     * @param string $userAgent
     */
    public function addAllowEntry($path, $userAgent = '*');
    
    /**
     * Add disallow enity
     * 
     * @param string $path
     * @param string $userAgent
     */
    public function addDisallowEntry($path, $userAgent = '*');
    
    /**
     * Set crawl delay
     * 
     * @param string $delay Delay in sec
     */
    public function setCrawlDelay($delay);
    
    /**
     * Add clean-param entry
     * 
     * @param string|array $parameters
     * @param string $path
     */
    public function addCleanParamEntry($parameters, $path);
    
    /**
     * Add sitemap
     * 
     * @param string $url
     */
    public function addSitemap($url);
    
    /**
     * Save robots.txt file
     * 
     * @param string $location
     */
    public function save($location);
    
}
