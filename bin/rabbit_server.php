<?php

$loader = require __DIR__ . '/../app/autoload.php';

require_once __DIR__ . '/../app/AppKernel.php';

$loop = \React\EventLoop\Factory::create();

$client = new \Bunny\Async\Client($loop, [
    "host" => "rabbitmq",
    "port" => 5672,
    "vhost" => "/",
    "user" => "guest",
    "password" => "guest",
]);

$consumer = new \Broker\Consumer($client);

$ping = new \Consumer\PingConsumer($loop);

$consumer->register($ping);

$consumer->run();

$loop->run();