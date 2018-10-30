<?php

namespace HomeBundle\Application\Task;

use inisire\ReactBundle\Threaded\Task;
use Symfony\Component\Process\Process;

class PingTask extends Task
{
    private $ips;

    /**
     * PingTask constructor.
     *
     * @param $ips
     */
    public function __construct($ips)
    {
        $this->ips = $ips;
        parent::__construct();
    }

    protected function doRun()
    {
        foreach ($this->ips as $ip) {
            $ping = new Process(['ping', '-w 1', '-c 1', $ip]);
            $ping->setTimeout(3000);
            $ping->run();
            $ping->wait();
            $output = $ping->getOutput();
            preg_match('#1 packets transmitted, (\d+) packets received, \d+% packet loss#', $output, $matches);

            if (count($matches) > 1 && $matches[1] > 0) {
                $this->result[$ip] = true;
            } else {
                $this->result[$ip] = false;
            }
        }

        $this->completed = true;
    }
}