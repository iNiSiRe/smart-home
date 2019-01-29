<?php


namespace HomeBundle\Consumer;


use Elasticsearch\Client;
use HomeBundle\Task\ElasticWriteTask;

class ElasticTaskConsumer implements ConsumerInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param ElasticWriteTask $task
     */
    public function consume(ElasticWriteTask $task)
    {
        $date = new \DateTime();

        $data = $task->data;

        $data['timestamp'] = time() * 1000;

        $index = 'log-' . $date->format('Y.m.d');

        if (!$this->client->exists(['index' => $index])) {
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

    /**
     * @param $task
     *
     * @return bool
     */
    public function isSupports($task)
    {
        return $task instanceof ElasticWriteTask;
    }

    /**
     * @return string
     */
    public function getQueueName()
    {
        return 'elastic_write_task';
    }
}