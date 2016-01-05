<?php namespace MtGTutor\Console\Commands;

use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class SetInfos
 * @package MtGTutor\Console\Commands
 */
class SetInfos extends Command
{
    /**
     * @var string
     */
    protected $url = 'http://magic.wizards.com/en/game-info/products/card-set-archive';

    /**
     * @var string
     */
    protected $imagePath = 'http://magic.wizards.com/sites/mtg/files/';

    /**
     * Configure command
     */
    protected function configure()
    {
        $this->setName('set:info')
            ->addArgument('set', InputArgument::REQUIRED, 'Which set do you want to select. Use the official three-letter code.')
            ->addOption('full-path', null, InputOption::VALUE_NONE, 'Add the full path to the images')
            ->addOption('date-format', null, InputOption::VALUE_OPTIONAL, 'Date Format', 'Y-m-d')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output result as json')
            ->setDescription('Get some basic information about a specific set')
            ->setHelp(
                <<<EOT
Gets the following information about a set:
 * Official Three-Letter Code
 * Set Name
 * Block
 * Number of Cards
 * Release Date
 * Logo
 * Icon

Usage:
Default usage to get a set (here Alara Reborn)
<info>mtgtutor-console set:info ARB</info>

You can specify a date format via the --date-format option
<info>mtgtutor-console set:info ARB --date-format=d.m.Y</info>

You can change the output format to json with the --json option
<info>mtgtutor-console set:info ARB --json</info>

You can add the full path to the files with --full-path
<info>mtgtutor-console set:info ARB --full-path</info>

You can combine all these options
<info>mtgtutor-console set:info ARB --date-format=d.m.Y --json --full-path</info>
EOT
            );
    }

    /**
     * Get set infos
     * @throws \InvalidArgumentException if no set is found
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init vars
        $exists = false;
        $data = [
            'code' => $input->getArgument('set'),
            'name' => null,
            'block' => null,
            'number' => null,
            'date' => null,
            'logo' => null,
            'icon' => null,
        ];

        // Fetch WotC website
        $client = new Client();
        $crawler = $client->request('GET', $this->url);

        $crawler->filter('.card-set-archive-table > ul > li > a > span.logo > img')->each(function (Crawler $node) use (&$client, &$data, &$exists) {
            $logo = $node->attr('src');

            if(strpos(strtolower($logo), strtolower($data['code'])) !== false) {
                // Set exists
                $exists = true;

                // Get Logo + Icon + Name
                $siblings = $node->parents()->first()->siblings();

                $data['logo'] = str_replace($this->imagePath, '', $logo);
                $data['icon'] = str_replace($this->imagePath, '', $siblings->eq(0)->children()->attr('src'));
                $data['name'] = trim($siblings->eq(1)->text());

                // Go to details page
                $link = $node->parents()->parents()->link();
                $crawler = $client->click($link);

                // Go to to info page (if exists)
                $anchor = $crawler->selectLink('Info');
                if ($anchor->count()) {
                    $link = $anchor->link();
                    $crawler = $client->click($link);
                }

                // Fetch block, number of cards and release date
                $crawler->filter('.tab-content.current > p')->each(function (Crawler $node, $i) use (&$data) {
                    if ($i % 2) {
                        // Get block and number of cards
                        if ($i == 1) {
                            preg_match('~<strong>Block:<\\/strong>.*<em>(.+?)</~m', $node->html(), $block);
                            preg_match('~<strong>Number of Cards:\s*<\/strong>[^\d]*(\d+)<?~m', $node->html(), $number);

                            $data['block'] = $block[1];
                            $data['number'] = (int)$number[1];
                        }

                        // get release date
                        if ($i == 5) {
                            preg_match('~<strong>Release Date:<\/strong>.+?([\w|,|\s]+)<~im', $node->html(), $release);
                            $data['date'] = $release[1];
                        }
                    }
                });
            }
        });

        // Could not find set
        if (!$exists) {
            throw new \InvalidArgumentException('Could not find set with the following code: ' . $data['code']);
        }

        // Format result
        $data = $this->formatResult($data, $input);

        // Ouput result
        $this->output($data, $input, $output);
    }

    /**
     * Format result
     * @param array                                           $data
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return array
     */
    protected function formatResult(array $data, InputInterface $input)
    {
        // change date format
        $date = new \DateTime(date('Y-m-d H:i:s', strtotime($data['date'])));
        $data['date'] = $date->format($input->getOption('date-format'));

        // add full path to url
        if ($input->getOption('full-path')) {
            $data['logo'] = $this->imagePath . $data['logo'];
            $data['icon'] = $this->imagePath . $data['icon'];
        }

        return $data;
    }

    /**
     * Write result to console
     * @param array                                             $data
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function output(array $data, InputInterface $input, OutputInterface $output)
    {
        // json output
        if ($input->getOption('json')) {
            $output->writeln(json_encode($data, JSON_PRETTY_PRINT));
        }

        // table output
        if (!$input->getOption('json')) {
            $table = new Table($output);
            $table
                ->setHeaders(['Code', 'Name', 'Block', 'Number', 'Release Date', 'Logo', 'Icon'])
                ->setRows([$data]);
            $table->render();
        }
    }
}
