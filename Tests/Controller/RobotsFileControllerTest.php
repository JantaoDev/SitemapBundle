<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Tests\Constroller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RobotsFileControllerTest extends WebTestCase
{
    
    protected $webDir;
    
    protected function setUp()
    {
        $this->webDir = realpath(__DIR__.'/../web').'/';
        $this->clearWebDir();
    }
    
    protected function tearDown()
    {
        parent::tearDown();
        $this->clearWebDir();
    }
    
    public function testIndexAction()
    {
        $client = static::createClient();
        
        try {
            $client->request('GET', '/robots.txt');
            $this->fail('Not found exception requred');
        } catch (NotFoundHttpException $e) {
        }
        
        file_put_contents($this->webDir.'robots.txt', 'default');
        $client->request('GET', '/robots.txt');
        $this->assertInstanceOf(BinaryFileResponse::class, $client->getResponse());
        $this->assertEquals('default', file_get_contents($client->getResponse()->getFile()->getPathname()));
    }

    public function testIndexActionWithSeveralHosts()
    {
        $client = static::createClient(['environment' => 'test_hosts']);
        $hosts  = static::$kernel->getContainer()->getParameter('jantao_dev_sitemap.hosts');
        
        foreach ($hosts as $host) {
            try {
                $client->request('GET', "http://$host/robots.txt");
                $this->fail('Not found exception requred');
            } catch (NotFoundHttpException $e) {
            }
            file_put_contents($this->webDir."robots.$host.txt", $host);
        }
        
        foreach ($hosts as $host) {
            $client->request('GET', "http://$host/robots.txt");
            $this->assertInstanceOf(BinaryFileResponse::class, $client->getResponse());
            $this->assertEquals($host, file_get_contents($client->getResponse()->getFile()->getPathname()));
        }
        
        $host = reset($hosts);
        $client->request('GET', "http://$host:8800/robots.txt");
        $this->assertInstanceOf(BinaryFileResponse::class, $client->getResponse());
        $this->assertEquals($host, file_get_contents($client->getResponse()->getFile()->getPathname()));
    }
    
    
    protected function clearWebDir()
    {
        foreach (glob($this->webDir.'*{.txt,.xml,.xml.gz}', GLOB_BRACE) as $file) {
            unlink($file);
        }
    }
    
}
