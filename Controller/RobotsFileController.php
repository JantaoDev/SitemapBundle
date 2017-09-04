<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Switching robots.txt files for several hosts
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class RobotsFileController extends Controller
{
    
    /**
     * Switch robots.txt files by requested host and configuration
     * 
     * @param Request $request
     * @return BinaryFileResponse
     * @throws NotFoundException
     */
    public function indexAction(Request $request)
    {
        $hosts = $this->getParameter('jantao_dev_sitemap.hosts');
        $webDir = rtrim($this->getParameter('jantao_dev_sitemap.web_dir'), '/');
        if (!empty($hosts)) {
            $host = $request->getHttpHost();
            if (strpos($host, ':') !== false) {
                $host = substr($host, 0, strpos($host, ':'));
            }
            if (in_array($host, $hosts) && file_exists("$webDir/robots.$host.txt")) {
                return new BinaryFileResponse("$webDir/robots.$host.txt");
            }
        }
        if (file_exists("$webDir/robots.txt")) {
            return new BinaryFileResponse("$webDir/robots.txt");
        } else {
            throw $this->createNotFoundException('robots.txt not found');
        }
    }
    
}
