<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Command;

use JantaoDev\SitemapBundle\Service\SitemapService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sitemap generation command
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class GenerateCommand extends Command
{

    protected $generator;

    public function __construct(SitemapService $generator)
    {
        parent::__construct();
        $this->generator = $generator;
    }


    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this->setDefinition(array(
        ))->setDescription('Generate sitemap')->setName('jantao_dev:sitemap:generate')->setHelp(<<<'EOF'
The <info>%command.name%</info> command generates sitemap file.
EOF
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write('Generating sitemap...');
        $this->generator->generate();
        $output->writeln('ok');
        return Command::SUCCESS;
    }

}