<?php namespace MtGTutor\Console\Test;

use MtGTutor\Console\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if name and version are set
     */
    public function testIfNameAndVersionAreSet()
    {
        $app = new Application();
        $this->assertContains($app::NAME, $app->getName());
        $this->assertEquals($app->getVersion(), $app::VERSION);
    }
}
