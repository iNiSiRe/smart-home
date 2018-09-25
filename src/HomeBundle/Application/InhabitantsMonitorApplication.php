<?php


namespace HomeBundle\Application;


use Monolog\Logger;
use React\EventLoop\LoopInterface;
use Symfony\Component\Process\Process;

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
     * @param LoopInterface $loop
     * @param array         $ips
     */
    public function __construct(LoopInterface $loop, $ips = [])
    {
        $this->ips = $ips;
        $this->loop = $loop;
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function loop()
    {
        $inhabitants = [];

        foreach ($this->ips as $ip) {
            $ping = new Process(['ping', '-w 1', '-c 1', $ip]);
            $ping->setTimeout(3000);
            $ping->run();
            $ping->wait();
            $output = $ping->getOutput();
            preg_match('#1 packets transmitted, (\d+) received, \d+% packet loss, time \d+ms#', $output, $matches);

            if (count($matches) > 1 && $matches[1] > 0) {
                $inhabitants[] = $ip;
            }

            if ($this->logger) {
                $this->logger->debug(sprintf('%s::%s -> ping result', __CLASS__, __METHOD__), ['out' => $output]);
            }
        }

        $this->inhabitants = $inhabitants;
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