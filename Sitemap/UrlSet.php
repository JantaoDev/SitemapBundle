<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Sitemap;

use JantaoDev\SitemapBundle\Sitemap\UrlSetInterface;
use JantaoDev\SitemapBundle\Sitemap\UrlInterface;
use JantaoDev\SitemapBundle\Exception\FileSavedException;
use JantaoDev\SitemapBundle\Sitemap\HostInterface;

/**
 * URL set class
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class UrlSet implements UrlSetInterface
{
    
    const MAX_ITEMS_COUNT = 49999;
    const MAX_SIZE = 10000000;
    
    protected $file;
    protected $fileName;
    protected $currentSize;
    protected $currentItemsCount;
    protected $lastMod;

    public function __construct()
    {
        $this->lastMod = new \DateTime('now');
        $this->createTemporaryFile();
        $this->writeTemporaryFile("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n");
    }

    protected function createTemporaryFile()
    {
        $this->fileName = tempnam(sys_get_temp_dir(), 'sitemap');
        $this->file = fopen($this->fileName, 'w');
        $this->currentSize = 0;
    }

    protected function writeTemporaryFile($data)
    {
        fwrite($this->file, $data);
        $this->currentSize += strlen($data);
    }

    protected function closeTemporatyFile()
    {
        fclose($this->file);
        $this->file = null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function add(HostInterface $host, UrlInterface $url)
    {
        if (!$this->file) {
            throw new FileSavedException();
        }
        $this->writeTemporaryFile($url->getXML($host));
        $this->currentItemsCount++;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->currentSize;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getItemsCount()
    {
        return $this->currentItemsCount;
    }
    
    /**
     * {@inheritdoc}
     */
    public function save($location, $gzip = false)
    {
        if (!$this->file) {
            throw new FileSavedException();
        }
        $this->writeTemporaryFile("</urlset>\n");
        $this->closeTemporatyFile();
        if (file_exists($location)) {
            unlink($location);
        }
        if ($gzip) {
            $sitemapFile = fopen($this->fileName, 'r');
            $sitemapFileGz = gzopen($location, 'wb9');
            while (!feof($sitemapFile)) {
                gzwrite($sitemapFileGz, fread($sitemapFile, 65536));
            }
            gzclose($sitemapFileGz);
            fclose($sitemapFile);
            unlink($this->fileName);
        } else {
            rename($this->fileName, $location);
        }
        $this->fileName = $location;
        chmod($this->fileName, 0644);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        return $this->fileName;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLastMod()
    {
        return $this->lastMod;
    }

    /**
     * {@inheritdoc}
     */
    public function isFull()
    {
        return ($this->currentSize >= self::MAX_SIZE) || ($this->currentItemsCount >= self::MAX_ITEMS_COUNT);
    }
    
}
