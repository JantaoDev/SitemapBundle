<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;

/**
 * Sitemap generation command
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class GenerateCommand extends ContainerAwareCommand
{

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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Generating sitemap...');
        $this->getContainer()->get('jantao_dev.sitemap')->generate();
        $output->writeln('ok');
    }

}