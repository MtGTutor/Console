<?php namespace MtGTutor\Console\Test\Commands;

use Goutte\Client;
use MtGTutor\Console\Test\TestTrait;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Constraints;

/**
 * Class SetInfosTest
 * @package MtGTutor\Console\Test\Commands
 */
class SetInfosTest extends \PHPUnit_Framework_TestCase
{
    use TestTrait;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scraperMock;

    /**
     * @var string
     */
    protected $path = __DIR__ . '/../data/';

    /**
     * Sets command
     */
    public function setUp()
    {
        parent::setUp();
        $this->setupApp();
        $this->setCommandTester('set:info');

        // Mock scraper, so we don't have to fetch the data from wizards in each test :)
        $this->scraperMock = $this->getMock('MtGTutor\Console\Scraper', ['request', 'click'], [new Client()]);
        $this->scraperMock
            ->expects($this->any())
            ->method('request')
            ->will(
                $this->returnValue(
                    new Crawler(
                        file_get_contents(
                            $this->path . 'http_magic.wizards.com_en_game-info_products_card-set-archive.htm'
                        ),
                        'http://www.example.com/'
                    )
                )
            );
    }

    /**
     * ARB Details page
     */
    protected function getARBDetailsMock()
    {
        $crawler = new Crawler(
            file_get_contents(
                $this->path . 'http_magic.wizards.com_en_game-info_products_card-set-archive_alara-reborn.htm'
            ),
            'http://www.example.com/'
        );

        $this->scraperMock
            ->expects($this->any())
            ->method('click')
            ->will(
                $this->returnValue($crawler)
            );
    }

    /**
     * Get OGW Page
     */
    protected function getOGWDetailsMock()
    {
        $crawler = new Crawler(
            file_get_contents(
                $this->path . 'http_magic.wizards.com_en_content_oath-gatewatch-home.htm'
            ),
            'http://www.example.com/'
        );

        $this->scraperMock
            ->expects($this->any())
            ->method('click')
            ->will(
                $this->returnValue($crawler)
            );
    }

    /**
     * Tests default execution
     */
    public function testExecute()
    {
        $this->getARBDetailsMock();
        $this->command->setScraper($this->scraperMock);
        $this->tester->execute(['command' => $this->command->getName(), 'set' => 'ARB']);

        $this->assertRegExp('/\bARB\b/i', $this->tester->getDisplay());
        $this->assertRegExp('/\bAlara Reborn\b/i', $this->tester->getDisplay());
        $this->assertRegExp('/\bShards of Alara\b/i', $this->tester->getDisplay());
        $this->assertRegExp('/\b145\b/i', $this->tester->getDisplay());
        $this->assertRegExp('/\b1970-01-01\b/i', $this->tester->getDisplay());
        $this->assertRegExp('/\bEN_ARB_SetLogo.png\b/i', $this->tester->getDisplay());
        $this->assertRegExp('/\bARB_SetIcon.png\b/i', $this->tester->getDisplay());
    }

    /**
     * Tests date format change
     */
    public function testDateFormat()
    {
        $this->getARBDetailsMock();
        $this->command->setScraper($this->scraperMock);
        $this->tester->execute([
            'command' => $this->command->getName(),
            'set' => 'ARB',
            '--date-format' => 'd.m.Y'
        ]);

        $this->assertRegExp('/\b01.01.1970\b/i', $this->tester->getDisplay());
    }

    /**
     * Tests correct json output
     */
    public function testJsonOutput()
    {
        $this->getOGWDetailsMock();
        $this->command->setScraper($this->scraperMock);

        $this->tester->execute([
            'command' => $this->command->getName(),
            'set' => 'OGW',
            '--json' => true,
            '--full-path' => true
        ]);

        $data = json_decode($this->tester->getDisplay(), true);

        $constraints = [
            'code' => [
                new Constraints\Type(['type' => 'string']),
                new Constraints\Length(['min' => 3, 'max' => 3])
            ],
            'name' => new Constraints\Type(['type' => 'string']),
            'number' => new Constraints\Type(['type' => 'integer']),
            'block' => new Constraints\Type(['type' => 'string']),
            'date' => new Constraints\Date(),
            'logo' => new Constraints\Url(),
            'icon' => new Constraints\Url(),
        ];

        $this->assertNotNull($data, 'Not valid JSON');
        $this->assertValidArray($constraints, $data);
    }

    /**
     * Tests if exception is thrown if no set is found
     * @expectedException \InvalidArgumentException
     */
    public function testSetNotFound()
    {
        $this->getARBDetailsMock();
        $this->command->setScraper($this->scraperMock);

        $this->tester->execute([
            'command' => $this->command->getName(),
            'set' => 'AAA',
        ]);
    }
}
