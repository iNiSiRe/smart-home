<?php

$loader = require __DIR__ . '/../app/autoload.php';

$loop = React\EventLoop\Factory::create();

$uri = getenv('RTSP_URI');
$cmd = sprintf('ffmpeg -i "%s" -f mpjpeg pipe:', $uri);

$ffmpeg = new \Service\FFmpeg($cmd);
$ffmpeg->start($loop);

$handle = fopen('var/logs/rtsp.log', 'a');
$file = new \React\Stream\WritableResourceStream($handle, $loop);

$server = new React\Http\Server(function (Psr\Http\Message\ServerRequestInterface $request) use ($ffmpeg, $loop, $file) {

    $file->write('request' . PHP_EOL);

    if ($request->getUri()->getPath() == '/status') {

        return new React\Http\Response(
            200,
            [
                'Content-Type' => 'application/json'
            ],
            json_encode(['working' => $ffmpeg->isWorking()])
        );

    } else {
        $stream = new \React\Stream\ThroughStream();

        $ffmpeg->stdout->on('data', $w = function ($chunk) use ($stream) {
            $stream->write($chunk);
        });

        $stream->on('close', function () use ($ffmpeg, $w) {
            $ffmpeg->stdout->removeListener('data', $w);
        });

        return new React\Http\Response(
            200,
            [
                'Accept-Ranges' => 'bytes',
                'Connection' => 'keep-alive',
                'Content-Type' => 'multipart/x-mixed-replace;boundary=ffmpeg'
            ],
            $stream
        );
    }
});

$server->on('error', function ($error) use ($file) {
    $file->write('error ' . get_class($error) . PHP_EOL);
});

$socket = new React\Socket\Server('0.0.0.0:9001', $loop);
$server->listen($socket);

$loop->run();