<?php

$loader = require __DIR__ . '/../app/autoload.php';

$loop = React\EventLoop\Factory::create();

$ffmpeg = new \React\ChildProcess\Process('ffmpeg -i "rtsp://admin:ju789lki@192.168.31.197:554/onvif1" -f mpjpeg pipe:');
$ffmpeg->start($loop);

$ffmpeg->on('exit', function () {
//    print 'ffmpeg exit' . PHP_EOL;
});

$received = 0;
$working = false;

$handle = fopen('dev.log', 'a');
$file = new \React\Stream\WritableResourceStream($handle, $loop);

$loop->addPeriodicTimer(1, function () use (&$received, &$working) {

    $working = $received > 0;
    $received = 0;

});

$ffmpeg->stdout->on('data', function ($chunk) use (&$received, $file) {

    $received += strlen($chunk);
    $file->write($received . PHP_EOL);

});

$server = new React\Http\Server(function (Psr\Http\Message\ServerRequestInterface $request) use ($ffmpeg, $loop, &$working) {

    if ($request->getUri()->getPath() == '/status') {

        return new React\Http\Response(
            200,
            [
                'Content-Type' => 'application/json'
            ],
            json_encode(['working' => $working])
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

$server->on('error', function ($error) {
    var_dump($error);
});

$socket = new React\Socket\Server('0.0.0.0:9001', $loop);
$server->listen($socket);

$loop->run();