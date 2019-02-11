<?php

$loader = require __DIR__ . '/../app/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('0.0.0.0:9002', $loop);

$socket->on('connection', function (React\Socket\ConnectionInterface $outer) use ($loop) {

    echo 'new outer connection' . PHP_EOL;

    $outer->pause();

    $connector = new React\Socket\Connector($loop);
    $connector->connect('192.168.31.197:554')->then(function (React\Socket\ConnectionInterface $inner) use ($loop, $outer) {

        echo 'inner connected' . PHP_EOL;

        $inner->on('data', function ($data) {

            echo '----- in ------' . PHP_EOL;
            echo $data;

        });

        $outer->on('data', function ($data) {

            echo '----- out ------' . PHP_EOL;
            echo $data;

        });

        $outer->pipe($inner);
        $inner->pipe($outer);

        $outer->resume();
    });

});

$loop->run();