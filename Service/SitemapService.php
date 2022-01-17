<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use JantaoDev\SitemapBundle\Sitemap\Sitemap;
use JantaoDev\SitemapBundle\Event\SitemapGenerateEvent;
use JantaoDev\SitemapBundle\Sitemap\Host;
use Symfony\Component\Routing\RouterInterface;
use JantaoDev\SitemapBundle\Sitemap\RobotsFileInterface;

/**
 * Sitemap generation service
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class SitemapService
{
 
    /**
     * @var EventDispatcherInterface 
     */
    protected $eventDispatcher;
    
    /**
     * @var RouterInterface 
     */
    protected $router;
    
    protected $hosts;
    protected $scheme;
    protected $port;
    protected $webDir;
    protected $gzip;
    protected $robots;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param RouterInterface $router
     * @param array $hosts
     * @param string $scheme 
     * @param int $port
     * @param string $webDir
     * @param bool $gzip
     * @param array $robots
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, RouterInterface $router, $hosts, $scheme, $port, $webDir, $gzip, $robots)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->router = $router;
        $this->hosts = $hosts;
        $this->scheme = $scheme;
        $this->port = $port;
        $this->webDir = rtrim($webDir, '/');
        $this->gzip = $gzip;
        $this->robots = $robots;
    }
    
    /**
     * Generate sitemap
     */
    public function generate()
    {
        if (empty($this->hosts)) {
            $host = $this->router->getContext()->getHost();
            $this->hosts = [$host];
        }
        if ($this->scheme === null) {
            $this->scheme = $this->router->getContext()->getScheme();
        }
        $schemeMode = Host::SCHEME_HTTP;
        if ($this->scheme == 'https') {
            $schemeMode = Host::SCHEME_HTTPS;
        } elseif ($this->scheme == 'https_only') {
            $schemeMode = Host::SCHEME_ONLY_HTTPS;
        }
        if (file_exists($this->webDir.'/robots.txt')) {
            unlink($this->webDir.'/robots.txt');
        }
        foreach ($this->hosts as $hostName) {
            $postfix = (count($this->hosts) > 1 ? '.'.$hostName : '');
            $host = new Host($hostName, $schemeMode, $this->port);
            $sitemap = new Sitemap($host);
            $this->configureRobotsFile($sitemap->getRobotsFile());
            $event = new SitemapGenerateEvent($sitemap);
            $this->eventDispatcher->dispatch($event, SitemapGenerateEvent::ON_SITEMAP_GENERATE);
            $sitemap->save($this->webDir, $this->gzip, $postfix);
        }
    }
    
    /**
     * Modify robots file by configuration
     * 
     * @param RobotsFileInterface $robots
     */
    protected function configureRobotsFile(RobotsFileInterface $robots)
    {
        $robots->setCrawlDelay($this->robots['crawl_delay']);
        foreach ($this->robots['allow'] as $path => $userAgent) {
            if (strpos($path, '/') === false) {
                $path = $this->router->generate($path);
            }
            $robots->addAllowEntry($path, $userAgent);
        }
        foreach ($this->robots['disallow'] as $path => $userAgent) {
            if (strpos($path, '/') === false) {
                $path = $this->router->generate($path);
            }
            $robots->addDisallowEntry($path, $userAgent);
        }
        foreach ($this->robots['clean_param'] as $path => $value) {
            $robots->addCleanParamEntry($value['parameters'], $path);
        }
    }
    
}
