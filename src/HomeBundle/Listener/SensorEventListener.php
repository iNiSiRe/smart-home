<?php
/**
 * Created by PhpStorm.
 * User: iNiSiRe
 * Date: 01.04.2016
 * Time: 2:42
 */

namespace HomeBundle\Listener;


use HomeBundle\Event\SensorEvent;
use Ratchet\ConnectionInterface;

class SensorEventListener
{
    /**
     * @var \SplObjectStorage
     */
    protected $subscribers;

    /**
     * SensorEventListener constructor.
     */
    public function __construct()
    {
        $this->subscribers = new \SplObjectStorage();
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function subscribe(ConnectionInterface $connection)
    {
        $this->subscribers->attach($connection);
    }

    /**
     * @param SensorEvent $event
     */
    public function onSensor(SensorEvent $event)
    {
        $data = [
            'type' => 'sensor',
            'name' => $event->getSensor()->getName(),
            'value' => $event->getValue()
        ];

        /**
         * @var ConnectionInterface $subscriber
         */
        foreach ($this->subscribers as $subscriber) {
            $subscriber->send(json_encode($data));
        }
    }
}