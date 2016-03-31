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
use HomeBundle\Entity\Unit;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

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
     * WebSocketServer constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $ws = new WsServer($this);
        $this->server = IoServer::factory($ws, 8000);
        $this->clients = new \SplObjectStorage();
        $this->entityManager = $entityManager;
    }

    /**
     * Run server
     */
    public function run()
    {
        $this->server->run();
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
        $data = json_decode($msg, true);
        $type = $data['type'];

        switch ($type) {
            case 'register':

                $room = $this->entityManager->getRepository('HomeBundle:Room')->findOneBy(['name' => $data['room']]);
                if (!$room) {
                    $room = (new Room())
                        ->setName($data['room']);
                    $this->entityManager->persist($room);
                    $this->entityManager->flush($room);
                }

                foreach ($data['sensors'] as $sensorData) {

                }

                $unit = (new Unit())
                    ->setRoom($room)
                    ->setName($data['unit']['name']);
                break;
        }
    }
}