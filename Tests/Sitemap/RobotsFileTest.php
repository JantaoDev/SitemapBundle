<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Tests\Sitemap;

use PHPUnit\Framework\TestCase;
use JantaoDev\SitemapBundle\Sitemap\Host;
use JantaoDev\SitemapBundle\Exception\FileSavedException;
use JantaoDev\SitemapBundle\Sitemap\RobotsFile;

class RobotsFileTest extends TestCase
{
    protected $webDir;
    protected $host;
    
    protected function parseAllowDisallowEntries($fileName)
    {
        $f = fopen($fileName, 'r');
        $userAgent = null;
        $result = [];
        while ($s = fgets($f)) {
            if (preg_match('/User\-agent:\s*([^\r\n#]+)/u', $s, $matches)) {
                $userAgent = trim($matches[1]);
            } elseif (preg_match('/Allow:\s*([^\r\n#]+)/u', $s, $matches)) {
                if (!$userAgent) {
                    $this->fail('Allow entry found but user-agent not specified');
                }
                if (!isset($result[$userAgent])) {
                    $result[$userAgent] = [];
                }
                if (!isset($result[$userAgent]['allow'])) {
                    $result[$userAgent]['allow'] = [];
                }
                $result[$userAgent]['allow'][] = trim($matches[1]);
            } elseif (preg_match('/Disallow:\s*([^\r\n#]+)/u', $s, $matches)) {
                if (!$userAgent) {
                    $this->fail('Disallow entry found but user-agent not specified');
                }
                if (!isset($result[$userAgent])) {
                    $result[$userAgent] = [];
                }
                if (!isset($result[$userAgent]['disallow'])) {
                    $result[$userAgent]['disallow'] = [];
                }
                $result[$userAgent]['disallow'][] = trim($matches[1]);
            } 
        }
        fclose($f);
        return $result;
    }
    
    
    protected function setUp():void
    {
        $this->webDir = realpath(__DIR__ . '/../public').'/';
        $this->host = $this->createMock(Host::class);
        $this->host->method('getHost')->willReturn('foo.com');
        $this->host->method('getHostWithScheme')->willReturn('http://foo.com');
        $this->host->method('getHostOptionalWithScheme')->willReturn('foo.com');
        $this->clearWebDir();
    }
    
    protected function tearDown():void
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
    
    public function testAddAllowDisallowEntry()
    {
        $robots = new RobotsFile($this->host);
        
        $robots->addAllowEntry('/test1');
        $robots->addAllowEntry('/test2');
        $robots->addAllowEntry('/test3/foo');
        $robots->addAllowEntry('/test4', 'AgentFoo');
        $robots->addAllowEntry('/test5', 'AgentBar');
        $robots->addAllowEntry('/test6', 'AgentFoo');
        $robots->addAllowEntry('/test7', 'AgentNoo');
        
        $robots->addDisallowEntry('/test11');
        $robots->addDisallowEntry('/test12');
        $robots->addDisallowEntry('/test13/foo');
        $robots->addDisallowEntry('/test14', 'AgentFoo');
        $robots->addDisallowEntry('/test15', 'AgentBar');
        $robots->addDisallowEntry('/test16', 'AgentFoo');
        $robots->addDisallowEntry('/test17', 'AgentYoo');
        
        $location = $this->webDir.'robots.txt';
        $robots->save($location);
        $result = $this->parseAllowDisallowEntries($location);
        
        $resultExpected = [
            '*' => [
                'allow' => ['/test1', '/test2', '/test3/foo'],
                'disallow' => ['/test11', '/test12', '/test13/foo']
            ],
            'AgentFoo' => [
                'allow' => ['/test4', '/test6'],
                'disallow' => ['/test14', '/test16']
            ],
            'AgentBar' => [
                'allow' => ['/test5'],
                'disallow' => ['/test15']
            ],
            'AgentNoo' => [
                'allow' => ['/test7']
            ],
            'AgentYoo' => [
                'disallow' => ['/test17']
            ]
        ];
        
        $this->assertEquals($resultExpected, $result);
    }
    
    public function testAddCleanParamEntry()
    {
        $robots = new RobotsFile($this->host);
        
        $robots->addCleanParamEntry('a=1&b=2', '/test1');
        $robots->addCleanParamEntry('c=3', '/test2');
        $robots->addCleanParamEntry(['d=4', 'e=5'], '/test3');
        
        $location = $this->webDir.'robots.txt';
        $robots->save($location);
        $content = file_get_contents($location);
        
        $this->assertRegexp('/^Clean\-param:\s*a=1&b=2\s+\/test1\s*$/um', $content);
        $this->assertRegexp('/^Clean\-param:\s*c=3\s+\/test2\s*$/um', $content);
        $this->assertRegexp('/^Clean\-param:\s*d=4&e=5\s+\/test3\s*$/um', $content);
    }
    
    public function testSetCrawlDelay()
    {
        $robots = new RobotsFile($this->host);
        
        $robots->setCrawlDelay(5);
        
        $location = $this->webDir.'robots.txt';
        $robots->save($location);
        $content = file_get_contents($location);
        
        $this->assertRegexp('/^Crawl\-delay:\s*5\s*$/um', $content);
    }
    
    public function testAddSitemap()
    {
        $robots = new RobotsFile($this->host);
        
        $robots->addSitemap('/sitemap1.xml');
        $robots->addSitemap('/sitemap2.xml');
        $robots->addSitemap('/sitemap3.xml');
        
        $location = $this->webDir.'robots.txt';
        $robots->save($location);
        $content = file_get_contents($location);
        
        $this->assertRegexp('/^Sitemap:\s*http:\/\/foo\.com\/sitemap1\.xml\s*$/um', $content);
        $this->assertRegexp('/^Sitemap:\s*http:\/\/foo\.com\/sitemap2\.xml\s*$/um', $content);
        $this->assertRegexp('/^Sitemap:\s*http:\/\/foo\.com\/sitemap3\.xml\s*$/um', $content);
    }
    
    public function testSave()
    {
        $robots = new RobotsFile($this->host);
        
        $location = $this->webDir.'robots.txt';
        $robots->save($location);
        $content = file_get_contents($location);
        
        $this->assertRegexp('/^Host:\s*foo\.com\s*$/um', $content);
        
        try {
            $robots->save($location);
            $this->fail('File should not be saved again');
        } catch (FileSavedException $e) {
        }
    }
            
}
