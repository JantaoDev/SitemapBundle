<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use JantaoDev\SitemapBundle\DependencyInjection\Compiler\AddSitemapListenersPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Bundle class
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class JantaoDevSitemapBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddSitemapListenersPass(), PassConfig::TYPE_OPTIMIZE);
    }
}
