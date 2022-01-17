<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Tests\Sitemap;

use PHPUnit\Framework\TestCase;
use JantaoDev\SitemapBundle\Sitemap\Host;

class HostTest extends TestCase
{
    
    /**
     * @dataProvider gettersProvider
     */
    public function testGetters($host, $hostWithScheme, $hostOptionalWithScheme, $inputHost, $schemeMode = 0, $port = null)
    {
        $hostObject = new Host($inputHost, $schemeMode, $port);
        
        $this->assertEquals($host, $hostObject->getHost());
        $this->assertEquals($hostWithScheme, $hostObject->getHostWithScheme());
        $this->assertEquals($hostOptionalWithScheme, $hostObject->getHostOptionalWithScheme());
    }
    
    public function gettersProvider()
    {
        return [
            ['foo.com', 'http://foo.com', 'foo.com', 'foo.com'],
            ['bar.com', 'http://bar.com', 'bar.com', 'bar.com'],
            ['foo.com', 'http://foo.com', 'foo.com', 'foo.com', Host::SCHEME_HTTP],
            ['foo.com', 'http://foo.com', 'foo.com', 'foo.com', Host::SCHEME_HTTP, 80],
            ['foo.com:8080', 'http://foo.com:8080', 'foo.com:8080', 'foo.com', Host::SCHEME_HTTP, 8080],
            ['foo.com', 'https://foo.com', 'foo.com', 'foo.com', Host::SCHEME_HTTPS],
            ['foo.com', 'https://foo.com', 'foo.com', 'foo.com', Host::SCHEME_HTTPS, 433],
            ['foo.com:434', 'https://foo.com:434', 'foo.com:434', 'foo.com', Host::SCHEME_HTTPS, 434],
            ['foo.com', 'https://foo.com', 'https://foo.com', 'foo.com', Host::SCHEME_ONLY_HTTPS],
            ['foo.com', 'https://foo.com', 'https://foo.com', 'foo.com', Host::SCHEME_ONLY_HTTPS, 433],
            ['foo.com:333', 'https://foo.com:333', 'https://foo.com:333', 'foo.com', Host::SCHEME_ONLY_HTTPS, 333],
        ];
    }
    
}
