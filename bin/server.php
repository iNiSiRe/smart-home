<?php

$loader = require __DIR__ . '/../app/autoload.php';

require_once __DIR__ . '/../app/AppKernel.php';

$env = $argv[1] == 'prod' ? 'prod' : 'dev';

$kernel = new AppKernel($env, $env == 'dev');
$kernel->boot();

$container = $kernel->getContainer();

$container->get('react.http.server')->start();

$container->get('HomeBundle\Service\Bootstrap')->boot();
$container->get('CommonBundle\Handler\MqttHandler')->start();

$pool = $container->get('worker.pool');

$container->get('react.loop')->run();