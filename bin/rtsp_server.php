<?php

$loader = require __DIR__ . '/../app/autoload.php';

$loop = React\EventLoop\Factory::create();

$uri = getenv('RTSP_URI');

//$cmd = sprintf('ffmpeg -i "%s" -f mpjpeg pipe:', $uri);

//$logger = new \Service\Logger($loop, 'var/logs/rtsp.log');
//
//$ffmpeg = new \Service\FFmpegWatcher($cmd, $loop, $logger);
//$ffmpeg->start();
//
//$handler = new \Handler\RtspRequestHandler($loop, $logger, $ffmpeg);
//$server = new React\Http\Server([$handler, 'handleRequest']);
//

//
//$server->on('error', function ($error) use ($logger) {
//    $logger->write('error', get_class($error));
//});
//
//$loop->addPeriodicTimer(60 * 15, function () use ($logger) {
//    $logger->write('info', "it's 15m tick");
//});
//
//$loop->addSignal(15, function () use ($loop, $logger) {
//    $logger->write('info', 'handle sigterm');
//    $loop->stop();
//});
//
//$socket = new React\Socket\Server('0.0.0.0:9001', $loop);
//$server->listen($socket);

//$size = 0;

//$factory = new \React\Datagram\Factory($loop);
//$factory->createServer('0.0.0.0:40300')->then(function (\React\Datagram\Socket $socket) use (&$size) {
//
//    $socket->on('message', function ($message, $address, $server) use (&$size) {
//
//        $size += strlen($message);
//
//    });
//
//});
//
//$loop->addPeriodicTimer(15, function () use (&$size) {
//
//    if ($size > 0) {
//        echo 'UDP received: '. $size . PHP_EOL;
//    }
//
//});

//$client = new \RTSP\Client($loop);
//
//$client->connect($uri);

$loop->run();