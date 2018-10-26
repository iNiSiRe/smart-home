<?php

use Symfony\Component\Config\Loader\LoaderInterface;

class ThreadKernel extends AppKernel implements \inisire\ReactBundle\Threaded\ThreadedKernelInterface
{
    /**
     * Thread number
     *
     * @var int
     */
    private $number = self::MAIN_THREAD;

//    public function getRootDir()
//    {
//        return __DIR__ . '/../app';
//    }
//
//    public function getProjectDir()
//    {
//        return __DIR__ . '/..';
//    }
//
//    public function getCacheDir()
//    {
//        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment() . '_thread';
//    }
//
//    public function getLogDir()
//    {
//        return dirname(__DIR__) . '/var/logs';
//    }
//
//    public function registerContainerConfiguration(LoaderInterface $loader)
//    {
//        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
//    }

    public function setThreadNumber(int $number)
    {
        $this->number = $number;
    }

    public function getThreadNumber()
    {
        return $this->number;
    }

    /**
     * @throws ReflectionException
     */
    protected function initializeContainer()
    {
        parent::initializeContainer();

        $container = $this->container;

        try {
            $reflection = new ReflectionClass($container);
            $property = $reflection->getProperty('privates');
            $property->setAccessible(true);
            $property->setValue($container, []);
        } catch (ReflectionException $e) {
            throw $e;
        }

        $this->container = $container;
    }
}
