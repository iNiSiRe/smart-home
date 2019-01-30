<?php

$loader = require __DIR__ . '/../app/autoload.php';

$uri = getenv('RTSP_URI');
$cmd = sprintf('ffmpeg -i "%s" -f mpjpeg pipe:', $uri);

$loop = React\EventLoop\Factory::create();
$logger = new \Service\Logger($loop, 'var/logs/rtsp.log');
$ffmpeg = new \Service\FFmpeg($cmd);
$handler = new \Handler\RtspRequestHandler($loop, $logger, $ffmpeg);
$server = new React\Http\Server([$handler, 'handleRequest']);

$server->on('error', function ($error) use ($logger) {
    $logger->write('error', get_class($error));
});

$loop->addSignal(15, function () use ($loop, $logger) {
    $logger->write('info', 'handle sigterm');
    $loop->stop();
});

$ffmpeg->start($loop);

$socket = new React\Socket\Server('0.0.0.0:9001', $loop);
$server->listen($socket);

$loop->run();