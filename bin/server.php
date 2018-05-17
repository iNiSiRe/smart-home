<?php

use HomeBundle\Listener\TestListener;
use inisire\ReactBundle\EventDispatcher\AsynchronousEventDispatcher;

$loader = require __DIR__ . '/../app/autoload.php';

require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$container->get('react.http.server')->start();
$container->get('home.web_socket_server');

$container->get('HomeBundle\Service\Bootstrap')->boot();
$container->get('CommonBundle\Handler\MqttHandler')->start();

$container->get('logger')->debug('1');

$dispatcher = $container->get(AsynchronousEventDispatcher::class);
$dispatcher->start();

$container->get('logger')->debug('1.5');

$listener = new TestListener($container->get('logger'));
$dispatcher->addListener('test', [$listener, 'onEvent']);

$container->get('logger')->debug('2');

$container->get('react.loop')->run();
