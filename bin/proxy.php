<?php

$loader = require __DIR__ . '/../app/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('0.0.0.0:10000', $loop);

$socket->on('connection', function (React\Socket\ConnectionInterface $outer) use ($loop) {

    $connector = new React\Socket\Connector($loop);

    $connector->connect('192.168.31.197:554')->then(function (React\Socket\ConnectionInterface $inner) use ($loop, $outer) {

        $inner->pipe($outer);
        $outer->pipe($inner);

    });

});

$loop->run();