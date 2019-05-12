<?php


namespace HomeBundle\Application;


use HomeBundle\Application\Task\PingTask;
use inisire\ReactBundle\Threaded\Pool;
use Monolog\Logger;
use React\EventLoop\LoopInterface;

class InhabitantsMonitorApplication
{
    /**
     * @var array
     */
    private $ips;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var array
     */
    private $inhabitants = [];

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @param LoopInterface $loop
     * @param array         $ips
     * @param Pool          $pool
     */
    public function __construct(LoopInterface $loop, $ips, Pool $pool)
    {
        $this->ips = $ips;
        $this->loop = $loop;
        $this->pool = $pool;
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function loop()
    {
        $this->pool->submit(new PingTask($this->ips), function ($result) {

            $inhabitants = [];

            foreach ($result as $ip => $alive) {
                if ($alive === true) {
                    $inhabitants[] = $ip;
                }
            }

            $this->inhabitants = $inhabitants;
        });
    }

    public function start()
    {
        $this->loop->addPeriodicTimer(60, [$this, 'loop']);
    }

    /**
     * @return array
     */
    public function getInhabitants()
    {
        return $this->inhabitants;
    }
}