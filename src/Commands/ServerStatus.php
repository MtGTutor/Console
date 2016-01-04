<?php namespace MtGTutor\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ServerStatus Command
 * @package MtGTutor\Console\Commands
 */
class ServerStatus extends Command
{
    /**
     * @var string
     */
    protected $host = 'www.mtg-tutor.de';

    /**
     * Configure command
     */
    protected function configure()
    {
        $this->setName('server:status')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Port of the server', 80)
            ->setDescription("Check if the server of {$this->host} is online")
            ->setHelp(
                <<<EOT
Checks if the server is online

Usage:

You can specify a specific port with --port or -p.
<info>mtgtutor-console server:status --port=443</info>
<info>mtgtutor-console server:status -p 443</info>

If you don't specify a port number it will set by default [80]
<info>mtgtutor-console server:status</info>
EOT
            );
    }

    /**
     * Checks if server is online or not
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = $input->getOption('port');
        $message = "Host {$this->host} at Port {$port} is";

        // ping server
        $fsock = @fsockopen($this->host, $port, $errno, $errstr, 6);

        // Online
        if ($fsock) {
            $style = new OutputFormatterStyle('white', 'green', ['bold']);
            $output->getFormatter()->setStyle('okay', $style);
            $output->writeln("<okay>$message online</okay>");
        }

        // offline
        if (!$fsock) {
            $output->writeln("<error>$message offline</error>");

            // print fsockopen error
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln("<info>$errstr ($errno)</info>");
            }
        }
    }
}
