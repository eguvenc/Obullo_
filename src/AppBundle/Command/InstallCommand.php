<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Interop\Container\ContainerInterface as Container;

class InstallCommand extends Command
{
    public function __construct(Container $container)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('install:app')
            ->setDescription('Install Service Providers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clear = $input->getArgument('clear');

    }
}
