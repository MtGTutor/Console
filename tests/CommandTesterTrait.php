<?php namespace MtGTutor\Console\Test;

use MtGTutor\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class CommandTesterTrait
 * @package MtGTutor\Console\Test
 */
trait CommandTesterTrait
{
    /**
     * @var \MtGTutor\Console\Application
     */
    private $app;

    /**
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    private $tester;

    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    private $command;

    /**
     * Sets command tester
     * @param string $command
     */
    public function setCommandTester($command)
    {
        $this->app = new Application();
        $this->app->setup();

        $this->command = $this->app->find($command);
        $this->tester = new CommandTester($this->command);
    }
}