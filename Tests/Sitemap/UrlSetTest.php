<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Tests\Sitemap;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use JantaoDev\SitemapBundle\Sitemap\UrlSet;
use JantaoDev\SitemapBundle\Sitemap\Host;
use JantaoDev\SitemapBundle\Sitemap\Url;
use JantaoDev\SitemapBundle\Exception\FileSavedException;

class UrlSetTest extends TestCase
{
    protected $webDir;
    protected $host;
    protected $url;
    
    protected function setUp()
    {
        $this->webDir = realpath(__DIR__.'/../web').'/';
        $this->host = $this->createMock(Host::class);
        $this->host->method('getHost')->willReturn('foo.com');
        $this->host->method('getHostWithScheme')->willReturn('http://foo.com');
        $this->host->method('getHostOptionalWithScheme')->willReturn('foo.com');
        $this->url = $this->createMock(Url::class);
        $this->url->method('getXML')->willReturn("<url>\n    <loc>http://foo.com/abc</loc>\n</url>\n");
        $this->clearWebDir();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->clearWebDir();
    }

    protected function clearWebDir()
    {
        foreach (glob($this->webDir.'*{.txt,.xml,.xml.gz}', GLOB_BRACE) as $file) {
            unlink($file);
        }
    }
    
    public function testAdd()
    {
        $urlSet = new UrlSet();
        
        $this->assertEquals(0, $urlSet->getItemsCount());
        $this->assertFalse($urlSet->isFull());
        
        $oldSize = $urlSet->getSize();
        $urlSet->add($this->host, $this->url);
        $this->assertEquals(1, $urlSet->getItemsCount());
        $this->assertFalse($urlSet->isFull());
        $this->assertGreaterThan($oldSize, $urlSet->getSize());
        
        $oldSize = $urlSet->getSize();
        $urlSet->add($this->host, $this->url);
        $this->assertEquals(2, $urlSet->getItemsCount());
        $this->assertFalse($urlSet->isFull());
        $this->assertGreaterThan($oldSize, $urlSet->getSize());
    }

    public function testSave()
    {
        $urlSet = new UrlSet();
        $urlSet->add($this->host, $this->url);
        
        $location = $this->webDir.'sitemap.xml';
        $urlSet->save($location);
        
        $xml = new \DOMDocument;
        $xml->load($location);
        $this->assertEquals(1, $xml->getElementsByTagName('url')->length);
        try {
            $urlSet->save($location);
            $this->fail('File should not be saved again');
        } catch (FileSavedException $e) {
        }
    }
    
}
