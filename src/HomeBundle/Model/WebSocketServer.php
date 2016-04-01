<?php
/**
 * Created by PhpStorm.
 * User: inisire
 * Date: 31.03.16
 * Time: 14:53
 */

namespace HomeBundle\Model;

use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\Room;
use HomeBundle\Entity\Sensor;
use HomeBundle\Entity\Unit;
use HomeBundle\Event\SensorEvent;
use HomeBundle\HomeEvents;
use HomeBundle\Listener\SensorEventListener;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\LoopInterface;
use React\Socket\Server;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WebSocketServer implements MessageComponentInterface
{
    /**
     * @var IoServer
     */
    protected $server;

    /**
     * @var \SplObjectStorage|
     */
    protected $clients;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var SensorEventListener
     */
    private $sensorEventListener;

    /**
     * WebSocketServer constructor
     *
     * @param LoopInterface       $loop
     * @param EntityManager       $entityManager
     * @param EventDispatcherInterface     $dispatcher
     * @param SensorEventListener $sensorEventListener
     */
    public function __construct(LoopInterface $loop, EntityManager $entityManager, EventDispatcherInterface $dispatcher, SensorEventListener $sensorEventListener)
    {
        $ws = new WsServer($this);

        $socket = new Server($loop);
        $socket->listen(8000, '0.0.0.0');

        $this->server = new IoServer(new HttpServer($ws), $socket, $loop);

        $this->clients = new \SplObjectStorage();

        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->sensorEventListener = $sensorEventListener;
    }

    /**
     * When a new connection is opened it will be passed to this method
     *
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     *
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        echo 'connected' . PHP_EOL;
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     *
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     *
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     *
     * @param  ConnectionInterface $conn
     * @param  \Exception          $e
     *
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

    /**
     * Triggered when a client sends data through the socket
     *
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string                       $msg  The message received
     *
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        $request = json_decode($msg, true);
        $type = $request['type'];

        switch ($type) {
            case 'register':

                $room = $this->entityManager->getRepository('HomeBundle:Room')->findOneBy(['name' => $request['room']]);
                if (!$room) {
                    $room = (new Room())
                        ->setName($request['room']);
                    $this->entityManager->persist($room);
                    $this->entityManager->flush($room);
                }

                $unit = (new Unit())
                    ->setRoom($room)
                    ->setName($request['unit']['name']);

                foreach ($request['sensors'] as $data) {
                    $sensor = (new Sensor())
                        ->setName($data['name'])
                        ->setClass($data['class'])
                        ->setUnit($unit);

                    $this->entityManager->persist($sensor);
                    $this->entityManager->flush($sensor);

                    $unit->addSensor($sensor);
                }

                $this->entityManager->persist($unit);
                $this->entityManager->flush($unit);

                break;

            case 'sensor':
                $sensor = $this->entityManager->getRepository('HomeBundle:Sensor')->findOneBy(['name' => $request['name']]);

                if (!$sensor) {
                    break;
                }

                $this->dispatcher->dispatch(HomeEvents::SENSOR_EVENT, new SensorEvent($sensor, $request['value']));

                break;

            case 'listen':
                $this->sensorEventListener->subscribe($from);
                break;
        }
    }
}