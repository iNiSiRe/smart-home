<?php


namespace React;


use React\EventLoop\LoopInterface;
use React\Promise\Deferred;

class Process extends \React\ChildProcess\Process
{
    private $data = "";

    public function execute(LoopInterface $loop, $timeout = null)
    {
        $deferred = new Deferred();

        $this->start($loop);

        $this->stdout->on('data', function ($chunk) {
            $this->data .= $chunk;
        });

        $this->on('exit', function ($code, $term) use ($deferred) {

            $deferred->resolve($this->data);

        });

        return $deferred->promise();
    }
}