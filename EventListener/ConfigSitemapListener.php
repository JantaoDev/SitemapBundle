<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\EventListener;

use JantaoDev\SitemapBundle\Service\SitemapListenerInterface;
use JantaoDev\SitemapBundle\Event\SitemapGenerateEvent;
use Symfony\Component\Routing\RouterInterface;
use JantaoDev\SitemapBundle\Sitemap\Url;
use Doctrine\ORM\EntityManagerInterface;
use JantaoDev\SitemapBundle\DoctrineIterator\DoctrineIterator;

/**
 * Event listener allows you to specify sitemap in the configuration file app/config
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class ConfigSitemapListener implements SitemapListenerInterface
{
    /**
     * @var RouterInterface 
     */
    protected $router;
    
    /**
     * @var EntityManagerInterface 
     */
    protected $entityManager;
    
    protected $sitemap;
    
    /**
     * @param RouterInterface $router
     * @param EntityManagerInterface $entityManager
     * @param array $sitemap Sitemap configuration loaded from app/config
     */
    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager, array $sitemap)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->sitemap = $sitemap;
    }

    /**
     * Get parameter value from entity or array
     * Format: ->index1->index2->index3
     * 
     * @param object|array $item
     * @param string $parameter
     * @return mixed Parameter value
     */
    protected function getItemParameter($item, $parameter)
    {
        if (substr($parameter, 0, 2) == '->') {
            $chain = substr($parameter, 2);
            $value = $item;
            while ($chain != '') {
                if (strpos($chain, '->') !== false) {
                    $index = substr($chain, 0, strpos($chain, '->'));
                    $chain = substr($chain, strpos($chain, '->') + 2);
                } else {
                    $index = $chain;
                    $chain = '';
                }
                if (is_array($value) && array_key_exists($index, $value)) {
                    $value = $value[$index];
                } elseif (is_object($value)) {
                    $method = 'get'.ucfirst($index);
                    if (method_exists($value, $method)) {
                        $value = $value->$method();
                    } elseif (property_exists($value, $index)) {
                        $value = $value->$index;
                    }
                }
            }
            return $value;
        }
        return $parameter;
    }
    
    /**
     * {@inheritdoc}
     */
    public function generateSitemap(SitemapGenerateEvent $event)
    {
        $sitemap = $event->getSitemap();
        $host = $sitemap->getHost()->getHost();
        foreach ($this->sitemap as $route => $configuration) {
            if (strpos($route, '@') !== false) {
                if (substr($route, strpos($route, '@') + 1) != $host) {
                    continue;
                }
                $route = substr($route, 0, strpos($route, '@'));
            }
            if (!$configuration['iterator']) {
                $url = $this->getUrlFromConfiguration($route, $configuration);
                $sitemap->add($url);
                continue;
            }
            if ($configuration['iterator'] == 'array') {
                $iterator = new \ArrayIterator($configuration['values']);
            } elseif ($configuration['iterator'] == 'doctrine') {
                $iterator = new DoctrineIterator($this->entityManager->createQuery($configuration['query']));
            } else {
                $iterator = new \EmptyIterator();
            }
            foreach ($iterator as $item) {
                $url = $this->getUrlFromConfiguration($route, $configuration, $item);
                $sitemap->add($url);
            }
        }
    }
    
    /**
     * Create URL object from route configuration
     * 
     * @param string $routeName
     * @param array $configuration
     * @param object|array $item
     * @return Url
     */
    protected function getUrlFromConfiguration($routeName, $configuration, $item = null)
    {
        foreach ($configuration['route_parameters'] as $key=>$parameter) {
            $configuration['route_parameters'][$key] = $this->getItemParameter($item, $parameter);
        }
        
        $url = $this->router->generate($routeName, $configuration['route_parameters']);
        
        $urlObject = new Url(
                $url,
                (isset($configuration['last_mod']) ? $this->getItemParameter($item, $configuration['last_mod']) : null),
                (isset($configuration['priority']) ? $this->getItemParameter($item, $configuration['priority']) : null),
                (isset($configuration['change_freq']) ? $this->getItemParameter($item, $configuration['change_freq']) : null)
        );
        return $urlObject;
    }

}
