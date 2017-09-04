<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Tests\Sitemap;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use JantaoDev\SitemapBundle\Sitemap\Url;
use JantaoDev\SitemapBundle\Sitemap\Host;

class UrlTest extends TestCase
{
    /**
     * @dataProvider getXmlProvider
     */
    public function testGetXml($xml, $url, $lastMod = null, $priority = null, $changeFreq = null)
    {
        $host = $this->createMock(Host::class);
        $host->method('getHostWithScheme')->willReturn('https://foo.com');
        
        $url = new Url($url, $lastMod, $priority, $changeFreq);
        $this->assertXmlStringEqualsXmlString($xml, $url->getXml($host));
    }
    
    public function getXmlProvider()
    {
        return [
            ['<url><loc>https://foo.com/</loc></url>', 'https://foo.com/'],
            ['<url><loc>https://foo.com/</loc></url>', 'http://bar.com/'],
            ['<url><loc>https://foo.com/abc</loc></url>', 'http://bar.com/abc'],
            ['<url><loc>https://foo.com/abc</loc></url>', '/abc'],
            ['<url><loc>https://foo.com/abcd?q=1</loc></url>', '/abcd?q=1'],
            ['<url><loc>https://foo.com/abcd?q=1&amp;b=2</loc></url>', '/abcd?q=1&b=2'],
            ['<url><loc>https://foo.com/</loc><lastmod>2012-12-22T12:13:14+00:00</lastmod></url>', '/', new \DateTime('2012-12-22 12:13:14', new \DateTimeZone('Europe/London'))],
            ['<url><loc>https://foo.com/</loc><lastmod>2017-02-23T13:14:15+02:00</lastmod></url>', '/', '2017-02-23T13:14:15+02:00'],
            ['<url><loc>https://foo.com/</loc><priority>0.1</priority></url>', '/', null, 0.1],
            ['<url><loc>https://foo.com/</loc><priority>0.8</priority></url>', '/', null, 0.8],
            ['<url><loc>https://foo.com/</loc><priority>1</priority></url>', '/', null, 1],
            ['<url><loc>https://foo.com/</loc><changefreq>always</changefreq></url>', '/', null, null, 'always'],
            ['<url><loc>https://foo.com/</loc><changefreq>never</changefreq></url>', '/', null, null, 'never'],
            ['<url><loc>https://foo.com/</loc><changefreq>hourly</changefreq></url>', '/', null, null, 'hourly'],
            ['<url><loc>https://foo.com/</loc><changefreq>daily</changefreq></url>', '/', null, null, 'daily'],
            ['<url><loc>https://foo.com/</loc><changefreq>weekly</changefreq></url>', '/', null, null, 'weekly'],
            ['<url><loc>https://foo.com/</loc><changefreq>monthly</changefreq></url>', '/', null, null, 'monthly'],
            ['<url><loc>https://foo.com/</loc><changefreq>yearly</changefreq></url>', '/', null, null, 'yearly'],
            ['<url><loc>https://foo.com/abc?q=3&amp;b=4</loc><lastmod>2015-02-01T00:00:12+00:00</lastmod><changefreq>yearly</changefreq></url>', '/abc?q=3&b=4', new \DateTime('2015-02-01 0:00:12', new \DateTimeZone('Europe/London')), null, 'yearly'],
            ['<url><loc>https://foo.com/abc?q=3&amp;b=4</loc><lastmod>2015-02-01T00:00:12+00:00</lastmod><priority>0.5</priority><changefreq>yearly</changefreq></url>', '/abc?q=3&b=4', new \DateTime('2015-02-01 0:00:12', new \DateTimeZone('Europe/London')), 0.5, 'yearly'],
        ];
    }
    
}
