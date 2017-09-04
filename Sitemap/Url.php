<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Sitemap;

use JantaoDev\SitemapBundle\Sitemap\UrlInterface;
use JantaoDev\SitemapBundle\Sitemap\HostInterface;

/**
 * Url class
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class Url implements UrlInterface
{
    
    protected $url;
    protected $lastMod;
    protected $changeFreq;
    protected $priority;

    /**
     * @param string $url
     * @param \DateTime $lastMod
     * @param string $priority
     * @param string $changeFreq
     */
    public function __construct($url, $lastMod = null, $priority = null, $changeFreq = null)
    {
        if ($lastMod && (!$lastMod instanceof \DateTime)) {
            $lastMod = new \DateTime($lastMod);
        }
        $query = parse_url($url, PHP_URL_QUERY);
        if ($query) {
            $query = '?'.$query;
        }
        $this->url = htmlentities(parse_url($url, PHP_URL_PATH).$query);
        $this->lastMod = $lastMod;
        $this->priority = $priority;
        $this->changeFreq = $changeFreq;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getXML(HostInterface $host)
    {
        $host = $host->getHostWithScheme();
        $record = "<url>\n";
        $record .= "    <loc>$host$this->url</loc>\n";
        if ($this->lastMod) {
            $record .= "    <lastmod>".$this->lastMod->format(\DateTime::W3C)."</lastmod>\n";
        }
        if ($this->priority) {
            $record .= "    <priority>$this->priority</priority>\n";
        }
        if ($this->changeFreq) {
            $record .= "    <changefreq>$this->changeFreq</changefreq>\n";
        }
        $record .= "</url>\n";
        return $record;
    }
    
}
