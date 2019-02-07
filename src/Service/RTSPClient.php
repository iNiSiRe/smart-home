<?php
/**
 * Created by PhpStorm.
 * User: inisire
 * Date: 2/7/19
 * Time: 6:10 PM
 */

namespace Service;


use React\EventLoop\LoopInterface;

class RTSPClient
{
    /**
     * @var LoopInterface
     */
    private $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function connect($uri)
    {
        $connector = new \React\Socket\Connector($this->loop);

        $connector->connect($uri)->then(function (\React\Socket\ConnectionInterface $inner) {

            $inner->pipe($outer);
            $outer->pipe($inner);

        });
    }
}