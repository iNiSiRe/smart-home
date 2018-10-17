<?php


namespace HomeBundle\Service;

use Elasticsearch\Client;

class DataStorage
{
    /**
     * @var Client
     */
    private $client;

    /**
     * DataStorage constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $type
     * @param array  $data
     */
    public function store(string $type, array $data)
    {
        $date = new \DateTime();

        $this->client->index([
            'index' => 'log-' . $date->format('Y.m.d'),
            'type' => $type,
            'body' => $data,
            'timestamp' => time()
        ]);
    }
}