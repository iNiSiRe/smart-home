<?php


namespace Consumer;

use React\EventLoop\LoopInterface;
use React\Process;
use React\Promise\Deferred;
use React\Promise\Promise;
use Task\PingTask;
use HomeBundle\Consumer\ConsumerInterface;

class PingConsumer implements ConsumerInterface
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    /**
     * @param PingTask $task
     *
     * @return Promise
     */
    public function consume($task)
    {
        $promises = [];

        foreach ($task->ips as $ip) {
            $ping = new Process('ping -w 1 -c 1 ' . $ip);

            $promises[] = $ping
                ->execute($this->loop)
                ->then(function ($output) use ($ip) {
                    preg_match('#1 packets transmitted, (\d+) packets received, \d+% packet loss#', $output, $matches);

                    if (count($matches) > 1 && $matches[1] > 0) {
                        return [$ip, true];
                    } else {
                        return [$ip, false];
                    }

                });
        }

        $deferred = new Deferred();

        \React\Promise\all($promises)->then(function ($values) use ($deferred) {

            $result = [];

            foreach ($values as $value) {
                list ($ip, $alive) = $value;
                $result[$ip] = $alive;
            }

            $deferred->resolve($result);
        });

        return $deferred->promise();
    }

    /**
     * @return string
     */
    public function getQueueName()
    {
        return 'rpc.ping';
    }

    /**
     * @param $task
     *
     * @return boolean
     */
    public function isSupport($task)
    {
        return $task instanceof PingTask;
    }
}