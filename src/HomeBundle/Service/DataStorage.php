<?php


namespace HomeBundle\Service;

use Elasticsearch\Client;
use inisire\ReactBundle\Threaded\ThreadedServiceInterface;

class DataStorage implements ThreadedServiceInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client        $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $type
     * @param array  $data
     *
     * @throws \Exception
     */
    public function store($type, array $data)
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