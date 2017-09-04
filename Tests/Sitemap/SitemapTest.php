<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Tests\Sitemap;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use JantaoDev\SitemapBundle\Sitemap\Host;
use JantaoDev\SitemapBundle\Sitemap\Url;
use JantaoDev\SitemapBundle\Sitemap\Sitemap;

class SitemapTest extends TestCase
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
    
    public function testSave()
    {
        $sitemap = new Sitemap($this->host);

        $sitemap->add($this->url);
        $sitemap->save($this->webDir, false, '.test');
        
        $this->assertFileExists($this->webDir.'robots.test.txt');
        $this->assertFileExists($this->webDir.'sitemap.test.xml');
    }
            
}
