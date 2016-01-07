<?php namespace MtGTutor\Console\Test;

use MtGTutor\Console\Application;
use PHPUnit_Framework_Assert as Assertion;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validation;

/**
 * Class CommandTesterTrait
 * @package MtGTutor\Console\Test
 */
trait TestTrait
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
     * @var array
     */
    private $errors = [];

    /**
     * Setup the app
     */
    protected function setupApp()
    {
        $this->app = new Application();
        $this->app->setup();
    }

    /**
     * Sets command tester
     * @param string $command
     */
    protected function setCommandTester($command)
    {
        $this->command = $this->app->find($command);
        $this->tester = new CommandTester($this->command);
    }

    /**
     * Validate an array against predefined rules.
     * @param array $constraints
     * @param array $input
     */
    protected function assertValidArray(array $constraints, array $input)
    {
        // validate rules
        $this->validateArray($constraints, $input);

        // log errors to console
        if (count($this->errors) >= 1) {
            Assertion::fail(implode(PHP_EOL, $this->errors));
        }
    }

    /**
     * Get validation rules and run validator
     * @param array $constraints
     * @param array $input
     */
    protected function validateArray(array $constraints, array $input)
    {
        // run validator
        $validator = Validation::createValidator();
        $violations = $validator->validate($input, new Constraints\Collection($constraints));

        // store errors
        foreach ($violations as $error) {
            $this->errors[] = "\e[1;31m× \033[0m" .
                'Field ' . $error->getPropertyPath() . ' caused an error: ' . $error->getMessage();
        }
    }
}
