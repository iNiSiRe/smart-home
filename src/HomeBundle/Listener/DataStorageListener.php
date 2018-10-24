<?php


namespace HomeBundle\Listener;

use Elasticsearch\Client;
use inisire\ReactBundle\EventDispatcher\Listener\EventListener;

class DataStorageListener extends EventListener
{
    const EVENT_DATA_STORAGE_STORE = 'event.data_storage.store';

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
     * @return string
     */
    public function getEvent()
    {
        return self::EVENT_DATA_STORAGE_STORE;
    }

    /**
     * @param mixed $event
     */
    public function onEvent($event)
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