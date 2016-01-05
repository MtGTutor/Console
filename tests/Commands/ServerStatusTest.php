<?php namespace MtGTutor\Console\Test\Commands;

use MtGTutor\Console\Test\TestTrait;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ServerStatusTest
 * @package MtGTutor\Console\Test\Commands
 */
class ServerStatusTest extends \PHPUnit_Framework_TestCase
{
    use TestTrait;

    /**
     * Sets command
     */
    public function setUp()
    {
        parent::setUp();
        $this->setupApp();
        $this->setCommandTester('server:status');
    }

    /**
     * Tests default execution
     */
    public function testExecute()
    {
        $this->tester->execute(['command' => $this->command->getName()]);

        $this->assertRegExp('/\bonline\b/i', $this->tester->getDisplay());
    }

    /**
     * Test Port option
     */
    public function testPortOption()
    {
        // long option
        $this->tester->execute([
            'command' => $this->command->getName(),
            '--port'    => 80,
        ]);

        $this->assertRegExp('/\bonline\b/i', $this->tester->getDisplay());

        // short syntax
        $this->tester->execute([
            'command' => $this->command->getName(),
            '-p'    => 80,
        ]);

        $this->assertRegExp('/\bonline\b/i', $this->tester->getDisplay());
    }

    /**
     * If server is offline there must be the offline notice
     * Simulated via unused port
     */
    public function testIfServerIfOffline()
    {
        $this->tester->execute(
            [
                'command' => $this->command->getName(),
                '-p'    => 1234, //unused port
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE
            ]
        );

        $this->assertRegExp('/\boffline\b/i', $this->tester->getDisplay());

        // Verbose output -> Error number
        $this->assertRegExp('/\(\d+\)/i', $this->tester->getDisplay());
    }
}
