<?php
/**
 * Created by PhpStorm.
 * User: inisire
 * Date: 31.03.16
 * Time: 14:53
 */

namespace HomeBundle\Model;

use Doctrine\ORM\EntityManager;
use Evenement\EventEmitter;
use HomeBundle\Actions;
use HomeBundle\Entity\Room;
use HomeBundle\Entity\Unit;
use HomeBundle\Entity\Module;
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
     * @var EventEmitter
     */
    protected $emitter;

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

        $this->emitter = new EventEmitter();
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
        echo 'open' . PHP_EOL;

        $this->clients->attach($conn);
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
        echo 'close' . PHP_EOL;

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
        echo 'error:' . get_class($e) . ' : ' . $e->getMessage() . PHP_EOL;

        if ($this->clients->offsetExists($conn)) {
            $conn->close();
            $this->clients->detach($conn);
        }
    }

    /**
     * @param ConnectionInterface $connection
     *
     * @return Module
     */
    protected function getUnit(ConnectionInterface $connection)
    {
        return $this->clients->offsetExists($connection)
            ? $this->clients->offsetGet($connection)
            : null;
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

        $action = $request['action'];

        switch ($action) {
            case Actions::ACTION_REGISTER:

                echo 'register' . PHP_EOL;

                // Room
                $room = $this->entityManager->getRepository('HomeBundle:Room')->findOneBy(['name' => $request['room']]);
                if (!$room) {
                    $room = (new Room())
                        ->setName($request['room']);
                    $this->entityManager->persist($room);
                    $this->entityManager->flush($room);
                }

                // Module
                $module = $this->entityManager->getRepository('HomeBundle:Module')->findOneBy([
                    'room' => $room,
                    'name' => $request['module']
                ]);

                if (!$module) {
                    $module = (new Module())
                        ->setAddress('0.0.0.0')
                        ->setRoom($room)
                        ->setName($request['module']);
                    $this->entityManager->persist($module);
                    $this->entityManager->flush($module);
                }

                // Units
                foreach ($request['units'] as $data) {

                    if ($module->hasUnit($data['name']))  {
                        continue;
                    }

                    $unit = (new Unit())
                        ->setType($data['type'] == 'sensor' ? Unit::TYPE_SENSOR : Unit::TYPE_CONTROLLER)
                        ->setName($data['name'])
                        ->setClass($data['class'])
                        ->setModule($module)
                        ->setRoom($room);

                    $this->entityManager->persist($unit);
                    $this->entityManager->flush($unit);

                    $module->addUnit($unit);
                }

                $this->entityManager->persist($module);
                $this->entityManager->flush($module);

                $this->clients->offsetSet($from, $module);

                $from->send('done');

                foreach ($module->getUnits() as $unit) {
                    if ($unit->getType() !== Unit::TYPE_CONTROLLER) {
                        continue;
                    }

                    echo 'Add listener to input' . PHP_EOL;

                    $listener = function ($room, $unit, $value) use ($from) {

                        echo 'Perform listener' . PHP_EOL;

                        $from->send(json_encode([
                            'action' => Actions::ACTION_CONTROL,
                            'resource' => 'input',
                            'room' => $room,
                            'unit' => $unit,
                            'value' => $value
                        ]));
                    };

                    $this->emitter->on(
                        sprintf(
                            'input.%s.%s.update',
                            $unit->getRoom()->getName(),
                            $unit->getName()),
                        $listener
                    );
                }

                break;

            case 'emit':
                $resource = $request['resource'];
                switch ($resource) {
                    case 'sensor':

                        if (!$module = $this->getUnit($from)) {
                            break;
                        }

                        $room = $module->getRoom()->getName();
                        $unit = $request['name'];
                        $event = sprintf('sensor.%s.%s.update', $room, $unit);
                        $value = $request['value'];
                        $this->emitter->emit($event, [$room, $unit, $value]);
                        break;

                    case 'input':
                        $room = $request['room'];
                        $unit = $request['name'];
                        $value = $request['value'];
                        $event = sprintf('input.%s.%s.update', $room, $unit);

                        echo sprintf('emit input event %s, %s, %s', $room, $unit, $value) . PHP_EOL;

                        $this->emitter->emit($event, [$room, $unit, $value]);
                        break;
                }
                break;

            case 'listen':
                $room = $request['room'];
                foreach ($request['sensors'] as $unit) {
                    $this->emitter->on(sprintf('sensor.%s.%s.update', $room, $unit), function ($room, $sensor, $value) use ($from) {
                        $data = [
                            'room' => $room,
                            'sensor' => $sensor,
                            'value' => $value
                        ];
                        $from->send(json_encode($data));
                    });
                }
                break;
        }
    }
}