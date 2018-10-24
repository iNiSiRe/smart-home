<?php

class KernelFactory implements \inisire\ReactBundle\EventDispatcher\KernelFactoryInterface
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
     * @return \inisire\ReactBundle\EventDispatcher\ThreadedKernelInterface
     */
    public function create()
    {
        $kernel = new AppKernel($this->env, false);
        $kernel->boot();

        return $kernel;
    }
}