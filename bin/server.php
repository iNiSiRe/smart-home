<?php

$loader = require __DIR__ . '/../app/autoload.php';

require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$container->get('react.http.server')->start();

$container->get('HomeBundle\Service\Bootstrap')->boot();
$container->get('CommonBundle\Handler\MqttHandler')->start();

$container->get('react.loop')->run();