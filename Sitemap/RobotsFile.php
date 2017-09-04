<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Sitemap;

use JantaoDev\SitemapBundle\Sitemap\RobotsFileInterface;
use JantaoDev\SitemapBundle\Exception\FileSavedException;
use JantaoDev\SitemapBundle\Sitemap\HostInterface;

/**
 * Robots.txt file class
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class RobotsFile implements RobotsFileInterface
{
    
    protected $host;
    protected $disallow = ['*' => []];
    protected $allow = ['*' => []];
    protected $crawlDelay;
    protected $cleanParam = [];
    protected $sitemap = [];
    protected $fileSaved = false;
    
    /**
     * @param HostInterface $host
     */
    public function __construct(HostInterface $host)
    {
        $this->host = $host;
    }
    
    /**
     * {@inheritdoc}
     */
    public function addAllowEntry($path, $userAgent = '*')
    {
        if (!isset($this->allow[$userAgent])) {
            $this->allow[$userAgent] = [];
        }
        $this->allow[$userAgent][] = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function addDisallowEntry($path, $userAgent = '*')
    {
        if (!isset($this->disallow[$userAgent])) {
            $this->disallow[$userAgent] = [];
        }
        $this->disallow[$userAgent][] = $path;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setCrawlDelay($delay)
    {
        $this->crawlDelay = $delay;
    }
    
    /**
     * {@inheritdoc}
     */
    public function addCleanParamEntry($parameters, $path)
    {
        if (is_array($parameters)) {
            $parameters = implode('&', $parameters);
        }
        $path = parse_url($path, PHP_URL_PATH);
        $this->cleanParam[] = "$parameters $path";
    }
    
    /**
     * {@inheritdoc}
     */
    public function addSitemap($path)
    {
        $this->sitemap[] = parse_url($path, PHP_URL_PATH);
    }
    
    /**
     * {@inheritdoc}
     */
    public function save($location)
    {
        if ($this->fileSaved) {
            throw new FileSavedException();
        }
        $file = fopen($location, 'w');
        $host = $this->host->getHostOptionalWithScheme();
        $sitemapHost = $this->host->getHostWithScheme();
        $userAgents = array_unique(array_merge(array_keys($this->allow), array_keys($this->disallow)));
        if (!in_array('*', $userAgents)) {
            array_unshift($userAgents, '*');
        }
        foreach ($userAgents as $userAgent) {
            fwrite($file, "User-agent: $userAgent\n");
            if (isset($this->allow[$userAgent])) {
                foreach ($this->allow[$userAgent] as $entry) {
                    fwrite($file, "Allow: $entry\n");
                }
            }
            if (isset($this->disallow[$userAgent])) {
                foreach ($this->disallow[$userAgent] as $entry) {
                    fwrite($file, "Disallow: $entry\n");
                }
            }
            if ($userAgent == '*') {
                fwrite($file, "Host: $host\n");
                if ($this->crawlDelay) {
                    fwrite($file, "Crawl-delay: $this->crawlDelay\n");
                }
            }
            fwrite($file, "\n");
        }
        if (count($this->cleanParam) > 0) {
            fwrite($file, "Clean-param: ".implode("\nClean-param:", $this->cleanParam)."\n\n");
        }
        if (count($this->sitemap) > 0) {
            fwrite($file, "Sitemap: $sitemapHost".implode("\nSitemap: $sitemapHost", $this->sitemap)."\n");
        }
        fclose($file);
        chmod($location, 0644);
        $this->fileSaved = true;
    }
    
}
