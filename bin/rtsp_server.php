<?php

$loader = require __DIR__ . '/../app/autoload.php';

$uri = getenv('RTSP_URI');
$cmd = sprintf('ffmpeg -min_port 40300 -max_port 40302 -i "%s" -vcodec mjpeg -s hd480 -qscale 0 -f mpjpeg pipe:', $uri);

$loop = React\EventLoop\Factory::create();
$logger = new \Service\Logger($loop, 'var/logs/rtsp.log');

$ffmpeg = new \Service\FFmpegWatcher($cmd, $loop, $logger);
$ffmpeg->start();

$handler = new \Handler\RtspRequestHandler($loop, $logger, $ffmpeg);
$server = new React\Http\Server([$handler, 'handleRequest']);

set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($logger) {

    $error = sprintf('[error] %s %s in %s:%s', $errno, $errstr, $errfile, $errline);
    file_put_contents('var/logs/rtsp.log', $error);

});

$server->on('error', function ($error) use ($logger) {
    $logger->write('error', get_class($error));
});

$loop->addPeriodicTimer(60 * 15, function () use ($logger) {
    $logger->write('info', "it's 15m tick");
});

$loop->addSignal(15, function () use ($loop, $logger) {
    $logger->write('info', 'handle sigterm');
    $loop->stop();
});

$socket = new React\Socket\Server('0.0.0.0:9001', $loop);
$server->listen($socket);

$loop->run();