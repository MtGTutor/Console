<?php namespace MtGTutor\Console;

use Goutte\Client;

/**
 * Class Scraper
 * @package MtGTutor\Console
 */
class Scraper
{
    /**
     * @var \Goutte\Client
     */
    protected $client;

    /**
     * Scraper constructor.
     * @param \Goutte\Client $client
     */
    public function __construct(Client $client)
    {
        $this->setClient($client);
    }

    /**
     * @return \Goutte\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \Goutte\Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param null   $uri
     * @param string $method
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function request($uri = null, $method = 'GET')
    {
        return $this->client->request($method, $uri);
    }

    /**
     * @param $link
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function click($link)
    {
        return $this->client->click($link);
    }
}
