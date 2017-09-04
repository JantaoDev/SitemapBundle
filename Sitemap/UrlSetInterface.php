<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Sitemap;

use JantaoDev\SitemapBundle\Sitemap\UrlInterface;
use JantaoDev\SitemapBundle\Sitemap\HostInterface;

/**
 * URL set interface
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
interface UrlSetInterface
{
    
    /**
     * Add URL to URL set
     * 
     * @param HostInterface $host
     * @param UrlInterface $url
     */
    public function add(HostInterface $host, UrlInterface $url);
    
    /**
     * Get current file size
     */
    public function getSize();
    
    /**
     * Get current items count
     */
    public function getItemsCount();
    
    /**
     * Save URL set to location
     * 
     * @param string $location
     * @param bool $gzip
     */
    public function save($location, $gzip = false);
    
    /**
     * Get current file location
     */
    public function getLocation();
    
    /**
     * Get last URL set modification
     */
    public function getLastMod();
    
    /**
     * Check URL set is full
     */
    public function isFull();
    
}
