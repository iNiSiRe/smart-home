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

print 'start' . PHP_EOL;

$executor = new \Broker\Executor($client);
$executor->run();

print 'end' . PHP_EOL;

$loop->addPeriodicTimer(1, function () use ($executor) {
    $executor
        ->call(new \Task\PingTask(['192.168.31.1']))
        ->then(function ($result) {
            var_dump($result);
        });
});

$loop->run();