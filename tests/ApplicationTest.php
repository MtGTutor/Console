<?php namespace MtGTutor\Console\Test;

/**
 * Class ApplicationTest
 * @package MtGTutor\Console\Test
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function setUp()
    {
        parent::setUp();
        $this->setupApp();
    }

    /**
     * Test if name and version are set
     */
    public function testIfNameAndVersionAreSet()
    {
        $app = $this->app;
        $this->assertContains($app::NAME, $app->getName());
        $this->assertEquals($app->getVersion(), $app::VERSION);
    }
}
