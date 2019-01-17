<?php

$loader = require __DIR__ . '/../app/autoload.php';

$loop = React\EventLoop\Factory::create();

$ffmpeg = new \React\ChildProcess\Process('ffmpeg -i "rtsp://admin:ju789lki@192.168.31.197:554/onvif1" -f mpjpeg pipe:');
$ffmpeg->start($loop);

$stream = new \React\Stream\ThroughStream();
$ffmpeg->stdout->pipe($stream);

$server = new React\Http\Server(function (Psr\Http\Message\ServerRequestInterface $request) use ($stream) {

    return new React\Http\Response(
        200,
        [
            'Accept-Ranges' => 'bytes',
            'Connection' => 'keep-alive',
            'Content-Type' => 'multipart/x-mixed-replace;boundary=ffmpeg'
        ],
        $stream
    );

});

$socket = new React\Socket\Server('0.0.0.0:9001', $loop);
$server->listen($socket);

$loop->run();