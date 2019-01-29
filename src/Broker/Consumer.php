<?php


namespace Broker;


use Bunny\Async\Client;
use Bunny\Channel;
use Bunny\Message;
use Bunny\Protocol\MethodQueueDeclareOkFrame;
use HomeBundle\Consumer\ConsumerInterface;
use function React\Promise\all;
use React\Promise\PromiseInterface;

class Consumer
{
    const MAX_CONCURRENT_TASKS = 5;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var ConsumerInterface[]
     */
    private $consumers = [];

    /**
     * @param Client $client
     *
     * @throws \Exception
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param ConsumerInterface $consumer
     */
    public function register(ConsumerInterface $consumer)
    {
        $this->consumers[] = $consumer;
    }

    /**
     * @param $task
     *
     * @return ConsumerInterface
     */
    private function findConsumer($task)
    {
        foreach ($this->consumers as $consumer) {

            if ($consumer->isSupport($task)) {
                return $consumer;
            }

        }

        return null;
    }

    /**
     * @param Message $message
     * @param Channel $channel
     * @param Client  $client
     */
    public function onConsume(Message $message, Channel $channel, Client $client)
    {
        print 'consume task ' . $message->content . PHP_EOL;

        $task = unserialize($message->content);

        $consumer = $this->findConsumer($task);

        $result = $consumer->consume($task);

        $process = function ($result) use ($channel, $message) {

            $result = serialize($result);

            print 'result: ' . $result . PHP_EOL;

            $channel->publish(
                $result,
                ['correlation_id' => $message->getHeader('correlation_id')],
                '',
                $message->getHeader('reply_to')
            )->then(function () {

                print 'done send response' . PHP_EOL;

            }, function () {

                print 'error send' . PHP_EOL;

            });

            $channel
                ->ack($message)
                ->then(function () {

                    print 'done ack response' . PHP_EOL;

                }, function () {

                    print 'error ack' . PHP_EOL;

                });
        };

        if ($result instanceof PromiseInterface) {
            $result->then($process);
        } else {
            $process($result);
        }

    }

    /**
     * Run
     */
    public function run()
    {
        $this->client->connect()
            ->then(function (Client $client) {

                print 'connect' . PHP_EOL;

                return $client->channel();

            })->then(function (Channel $channel) {

                print 'channel' . PHP_EOL;

                return all([$channel->queueDeclare('rpc'), $channel]);

            })->then(function ($values) {

                list($frame, $channel) = $values;

                print 'queue ' . $frame->queue . PHP_EOL;

                return $channel->consume([$this, 'onConsume'], $frame->queue);

            });
    }
}