<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use JantaoDev\SitemapBundle\Service\SitemapService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use JantaoDev\SitemapBundle\Event\SitemapGenerateEvent;
use JantaoDev\SitemapBundle\Sitemap\Url;

class GenerateCommandTest extends WebTestCase
{
    
    protected $webDir;
    
    protected function setUp()
    {
        $client = static::createClient();
        $this->webDir = realpath(__DIR__.'/../web').'/';
        $this->clearWebDir();
        static::$kernel->getContainer()->get('event_dispatcher')->addListener(
            SitemapGenerateEvent::ON_SITEMAP_GENERATE,
            function (SitemapGenerateEvent $event) {
                $url = new Url('/test');
                $event->getSitemap()->add($url);
            }
        );
    }
    
    protected function tearDown()
    {
        parent::tearDown();
        $this->clearWebDir();
    }
    
    public function testExecuteAction()
    {
        $application = new Application(self::$kernel);
        
        $command = $application->find('jantao_dev:sitemap:generate');
        $commandTester = new CommandTester($command);
        $result = $commandTester->execute(['command' => $command->getName()]);
        
        $this->assertEquals(0, $result);
        $this->assertFileExists($this->webDir.'robots.txt');
        $this->assertFileExists($this->webDir.'sitemap.xml');
    }

    protected function clearWebDir()
    {
        foreach (glob($this->webDir.'*{.txt,.xml,.xml.gz}', GLOB_BRACE) as $file) {
            unlink($file);
        }
    }
    
}
