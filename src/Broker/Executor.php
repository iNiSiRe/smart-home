<?php


namespace Broker;


use Bunny\Channel;
use Bunny\Async\Client;
use Bunny\Message;
use Bunny\Protocol\MethodQueueDeclareOkFrame;
use Evenement\EventEmitter;
use React\Promise\Deferred;

class Executor extends EventEmitter
{
    /**
     * @var Channel
     */
    private $channel;

    /**
     * @var Deferred[]
     */
    private $awaits = [];

    /**
     * @var string
     */
    private $replyQueue;

    /**
     * @var Client
     */
    private $client;

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
     * @return \React\Promise\PromiseInterface
     */
    public function run()
    {
        return $this->client
            ->connect()
            ->then(function (Client $client) {

                print 'connect' . PHP_EOL;

                return $client->channel();

            })->then(function (Channel $channel) {

                print 'channel' . PHP_EOL;

                $this->channel = $channel;

                return \React\Promise\all([
                    $channel,
                    $this->channel->queueDeclare('', false, false, false, true)
                ]);

            })->then(function ($values) {

                print 'declare' . PHP_EOL;

                /**
                 * @var Channel                   $channel
                 * @var MethodQueueDeclareOkFrame $frame
                 */
                list ($channel, $frame) = $values;

                $this->replyQueue = $frame->queue;

                return $channel->consume(function (Message $message, Channel $channel, Client $client) {

                    print 'consume result' . PHP_EOL;

                    if (!$message->hasHeader('correlation_id')) {
                        return;
                    }

                    $id = $message->getHeader('correlation_id');

                    if (!isset($this->awaits[$id])) {
                        return;
                    }

                    $deferred = $this->awaits[$id];
                    unset($this->awaits[$id]);

                    $deferred->resolve(unserialize($message->content));

                    $channel->ack($message);

                }, $frame->queue);

            });
    }

    public function call(Task $task)
    {
        if (!$this->channel) {
            throw new \RuntimeException('Connection isn\'t established');
        }

        $id = uniqid();

        $this->channel->publish(
            serialize($task),
            [
                'reply_to'       => $this->replyQueue,
                'correlation_id' => $id
            ],
            '',
            'rpc'
        );

        $deferred = new Deferred();

        $this->awaits[$id] = $deferred;

        return $deferred->promise();
    }
}