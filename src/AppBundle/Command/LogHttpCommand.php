<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Interop\Container\ContainerInterface as Container;

class LogHttpCommand extends Command
{
    public function __construct(Container $container)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('log:http')
            ->setDescription('Follow log files')
            ->addArgument(
                'clear',
                InputArgument::OPTIONAL,
                'Do you want to clear http log file ?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file  = ROOT . 'src/AppBundle/Data/http.log';
        $clear = $input->getArgument('clear');

        /**
         * Clear log file
         */
        if ($clear == 'clear') {
            if (is_file($file)) {
                unlink($file);
            }
            $output->writeln('<info>Log file deleted successfully.</info>');
            return;
        }
        /**
         * Display log file
         */
        $size = 0;
        while (true) {
            clearstatcache();
            if (! file_exists($file)) { // Start process when file exists.
                continue;
            }
            $currentSize = filesize($file); // Continue the process when file size change.
            if ($size == $currentSize) {
                usleep(50);
                continue;
            }
            if (! $fh = fopen($file, 'rb')) {
                $output->writeln('<error>You haven\'t got a write permission to data folder.</error>');
                die;
            }
            fseek($fh, $size);
            while ($line = fgets($fh)) {
                /**
                 * Colourize sql queries (green)
                 */
                if (stripos($line, 'SQL-') !== false) {
                    $line = "<fg=green;options=bold>".preg_replace('/[\s]+/', ' ', $line)."</>";
                    $line = preg_replace('/[\r\n]/', "\n", $line)."\n";
                }
                /**
                 * Colourize errors (red)
                 */
                if (stripos($line, '.ERROR') !== false) {
                    $line = "<fg=red;options=bold>".$line."</>";
                }
                $output->write($line);
            }
            fclose($fh);
            clearstatcache();
            $size = $currentSize;
        }
    }
}
