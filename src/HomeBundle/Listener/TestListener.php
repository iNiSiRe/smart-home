<?php
/**
 * Created by PhpStorm.
 * User: user18
 * Date: 15.03.18
 * Time: 11:46
 */

namespace HomeBundle\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;

class TestListener implements ContainerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * TestListener constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    function onEvent(Event $event)
    {
        $a = new SomeDich();

        $this->logger->debug('start');

        sleep(3);

        $rooms = $this->container->get('doctrine.orm.entity_manager')->getRepository('HomeBundle:Room')->findAll();

        $this->logger->debug('listener, rooms', [count($rooms)]);

        $curl = curl_init('https://google.com');
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);

        $this->logger->debug('listener, request', [strlen($data)]);

        $this->logger->debug('end');
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}