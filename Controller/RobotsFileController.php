<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Switching robots.txt files for several hosts
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class RobotsFileController extends AbstractController
{
    
    /**
     * Switch robots.txt files by requested host and configuration
     * 
     * @param Request $request
     * @return BinaryFileResponse
     * @throws NotFoundHttpException
     */
    public function index(Request $request): BinaryFileResponse
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
