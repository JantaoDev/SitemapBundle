<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use JantaoDev\SitemapBundle\Service\SitemapService;
use JantaoDev\SitemapBundle\Event\SitemapGenerateEvent;
use JantaoDev\SitemapBundle\Sitemap\Url;

class SitemapServiceTest extends WebTestCase
{
    
    protected $defaultRobotsConfig = ['allow' => [], 'disallow' => [], 'crawl_delay' => null, 'clean_param' => []];
    
    protected $webDir;
    protected $eventDispatcher;
    protected $router;
    
    protected function setUp()
    {
        $this->webDir = realpath(__DIR__.'/../web').'/';
        static::createClient();
        $container  = static::$kernel->getContainer();
        $this->eventDispatcher = $container->get('event_dispatcher');
        $this->router = $container->get('router');
        $this->eventDispatcher->addListener(
            SitemapGenerateEvent::ON_SITEMAP_GENERATE,
            function (SitemapGenerateEvent $event) {
                $url = new Url('/test');
                $event->getSitemap()->add($url);
            }
        );
        $this->clearWebDir();
    }
    
    protected function tearDown()
    {
        parent::tearDown();
        $this->clearWebDir();
    }
    
    public function testGenerate()
    {
        $oneHostSitemapService = new SitemapService($this->eventDispatcher, $this->router, ['foo.com'], 'http', null, $this->webDir, false, $this->defaultRobotsConfig);
        $oneHostSitemapService->generate();
        $this->assertFileExists($this->webDir.'robots.txt');
        $this->assertFileExists($this->webDir.'sitemap.xml');
        
        $this->clearWebDir();
        $severalHostsSitemapService = new SitemapService($this->eventDispatcher, $this->router, ['foo.com', 'bar.com'], 'http', null, $this->webDir, false, $this->defaultRobotsConfig);
        $severalHostsSitemapService->generate();
        $this->assertFileExists($this->webDir.'robots.foo.com.txt');
        $this->assertFileExists($this->webDir.'sitemap.foo.com.xml');
        $this->assertFileExists($this->webDir.'robots.bar.com.txt');
        $this->assertFileExists($this->webDir.'sitemap.bar.com.xml');
    }
    
    protected function clearWebDir()
    {
        foreach (glob($this->webDir.'*{.txt,.xml,.xml.gz}', GLOB_BRACE) as $file) {
            unlink($file);
        }
    }
    
}
