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
use HomeBundle\MessageProcessor\MessageProcessorFactory;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\LoopInterface;
use React\Socket\Server;
use Symfony\Bridge\Monolog\Logger;
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
     * @var Logger
     */
    private $logger;

    /**
     * @var MessageProcessorFactory
     */
    private $messageProcessorFactory;

    /**
     * WebSocketServer constructor
     *
     * @param LoopInterface $loop
     * @param Logger $logger
     * @param MessageProcessorFactory $messageProcessorFactory
     */
    public function __construct(LoopInterface $loop, Logger $logger, MessageProcessorFactory $messageProcessorFactory)
    {
        $ws = new WsServer($this);

        $socket = new Server($loop);
        $socket->listen(8000, '0.0.0.0');

        $this->server = new IoServer(new HttpServer($ws), $socket, $loop);
        $this->clients = new \SplObjectStorage();
        $this->logger = $logger;
        $this->messageProcessorFactory = $messageProcessorFactory;
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
        $this->logger->info('open');
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
        $this->logger->info('close');
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
        $this->logger->error('error:' . get_class($e) . ' : ' . $e->getMessage());

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
        $this->logger->info('message: ' . $msg);

        $request = json_decode($msg, true);

        $action = $request['action'];

        if ($action == Actions::ACTION_PING) {
            return;
        }

        $processor = $this->messageProcessorFactory->create($action);

        $processor->process($from, $request);
    }
}