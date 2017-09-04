<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Sitemap;

/**
 * Host object interface
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
interface HostInterface
{
    
    /**
     * Get host
     * Example: foo.com
     * 
     * @return string
     */
    public function getHost();
    
    /**
     * Get host with scheme
     * Example: https://foo.com
     * 
     * @return string
     */
    public function getHostWithScheme();
    
    /**
     * Get host optionaly with scheme
     * This method uses for robots.txt host parameter. The scheme will be added if the host is available only for the scheme.
     * 
     * @return string
     */
    public function getHostOptionalWithScheme();
    
}
