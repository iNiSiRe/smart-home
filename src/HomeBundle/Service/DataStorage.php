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

        $data['timestamp'] = time() * 1000;

        $index = 'log-' . $date->format('Y.m.d');

        if (!$this->client->indices()->exists(['index' => $index])) {
            $this->client->indices()->create([
                'index' => $index,
                'body' => [
                    'mappings' => [
                        '_doc' => [
                            'properties' => [
                                'timestamp' => ['type' => 'date']
                            ]
                        ]
                    ]
                ]
            ]);
        }

        $this->client->index([
            'index' => $index,
            'type' => '_doc',
            'body' => $data
        ]);
    }
}