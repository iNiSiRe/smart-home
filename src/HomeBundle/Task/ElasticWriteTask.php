<?php


namespace HomeBundle\Task;


use Elasticsearch\Client;
use inisire\ReactBundle\Threaded\Task;

class ElasticWriteTask extends Task
{
    private $data;

    /**
     * @param array $data
     */
    public function __construct($data)
    {
        $this->data = $data;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function doRun()
    {
        $client = $this->getKernel()->getContainer()->get(Client::class);

        $date = new \DateTime();

        $data = $this->data;

        $data['timestamp'] = time() * 1000;

        $index = 'log-' . $date->format('Y.m.d');

        if (!$client->indices()->exists(['index' => $index])) {
            $client->indices()->create([
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

        $client->index([
            'index' => $index,
            'type' => '_doc',
            'body' => $data
        ]);
    }
}