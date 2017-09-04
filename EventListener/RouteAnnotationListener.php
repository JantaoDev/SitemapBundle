<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\EventListener;

use JantaoDev\SitemapBundle\Service\SitemapListenerInterface;
use JantaoDev\SitemapBundle\Event\SitemapGenerateEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;
use JantaoDev\SitemapBundle\Sitemap\Url;

/**
 * Event listener allows you to use annotations to include routes in the sitemap file
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class RouteAnnotationListener implements SitemapListenerInterface
{
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function generateSitemap(SitemapGenerateEvent $event)
    {
        $routeCollection = $this->router->getRouteCollection();
        $sitemap = $event->getSitemap();
        
        foreach ($routeCollection->all() as $name => $route) {
            $url = $this->getUrlFromRoute($name, $route);
            if ($url) {
                $sitemap->add($url);
            }
        }
    }
    
    /**
     * Create URL object from route annotations
     * 
     * @param string $name
     * @param Route $route
     * @return Url|null
     * @throws \InvalidArgumentException
     */
    protected function getUrlFromRoute($name, Route $route)
    {
        $option = $route->getOption('sitemap');
        
        if ($option === null) {
            return null;
        }
        if (is_string($option)) {
            $decoded = json_decode($option, true);
            if (!json_last_error() && is_array($decoded)) {
                $option = $decoded;
            }
        }
        if (!is_array($option) && !is_bool($option)) {
            $bool = filter_var($option, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (null === $bool) {
                throw new \InvalidArgumentException("The sitemap option for route $name must be boolean or array");
            }
            $option = $bool;
        }
        if (!$option) {
            return null;
        }
        $url = $this->router->generate($name);
        
        $urlObject = new Url(
                $url,
                (isset($option['lastMod']) ? $option['lastMod'] : null),
                (isset($option['priority']) ? $option['priority'] : null),
                (isset($option['changeFreq']) ? $option['changeFreq'] : null)
        );
        
        return $urlObject;
    }

}
