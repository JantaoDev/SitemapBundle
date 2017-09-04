<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Sitemap;

use JantaoDev\SitemapBundle\Sitemap\SitemapInterface;
use JantaoDev\SitemapBundle\Sitemap\UrlInterface;
use JantaoDev\SitemapBundle\Sitemap\RobotsFile;
use JantaoDev\SitemapBundle\Sitemap\UrlSet;
use JantaoDev\SitemapBundle\Sitemap\HostInterface;

/**
 * Sitemap class
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class Sitemap implements SitemapInterface
{

    /**
     * @var RobotsFileInterface
     */
    protected $robotsFile;
    
    /**
     * @var array 
     */
    protected $urlSets = [];
    
    /**
     * @var UrlSetInterface
     */
    protected $currentUrlSet;
    
    /**
     * @var HostInterface
     */
    protected $host;

    /**
     * @param HostInterface $host
     */
    public function __construct(HostInterface $host)
    {
        $this->host = $host;
        $this->robotsFile = new RobotsFile($host);
        $this->currentUrlSet = new UrlSet();
        $this->urlSets[] = $this->currentUrlSet;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRobotsFile()
    {
        return $this->robotsFile;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }
    
    /**
     * {@inheritdoc}
     */
    public function add(UrlInterface $url)
    {
        $this->currentUrlSet->add($this->host, $url);
        if ($this->currentUrlSet->isFull()) {
            $this->currentUrlSet = new UrlSet();
            $this->urlSets[] = $this->currentUrlSet;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function save($folder, $gzip = false, $postfix = null)
    {
        $index = null;
        $gzipExtention = ($gzip ? '.gz' : '');
        foreach ($this->urlSets as $urlSet) {
            if ($urlSet->getItemsCount() == 0) {
                continue;
            }
            $sitemapName = "sitemap$index$postfix.xml$gzipExtention";
            $urlSet->save("$folder/$sitemapName", $gzip);
            $this->robotsFile->addSitemap("/$sitemapName");
            $index++;
        }
        $this->robotsFile->save("$folder/robots$postfix.txt");
    }
    
}
