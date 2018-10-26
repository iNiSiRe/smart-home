<?php

class KernelFactory implements \inisire\ReactBundle\Threaded\KernelFactoryInterface
{
    private $env;

    /**
     * @param $env
     */
    public function __construct($env)
    {
        $this->env = $env;
    }

    /**
     * @return \inisire\ReactBundle\Threaded\ThreadedKernelInterface
     */
    public function create()
    {
        $kernel = new ThreadKernel($this->env, $this->env == 'dev');
        $kernel->boot();

        return $kernel;
    }
}