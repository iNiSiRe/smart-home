<?php

namespace Handler;


use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use Service\FFmpeg;
use Service\FFmpegWatcher;
use Service\Logger;

class RtspRequestHandler
{
    /**
     * @var \Service\FFmpegWatcher
     */
    private $ffmpeg;

    /**
     * @var \Service\Logger
     */
    private $logger;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var int
     */
    private $count = 0;

    /**
     * RtspRequestHandler constructor.
     *
     * @param LoopInterface $loop
     * @param Logger $logger
     * @param FFmpegWatcher $ffmpeg
     */
    public function __construct(LoopInterface $loop, Logger $logger, FFmpegWatcher $ffmpeg)
    {
        $this->loop = $loop;
        $this->logger = $logger;
        $this->ffmpeg = $ffmpeg;
    }

    public function handleRequest(ServerRequestInterface $request)
    {
        $count = $this->count++;

        $this->logger->write('info', 'request ' . $count);

        if ($request->getUri()->getPath() == '/status') {

            return new \React\Http\Response(
                200,
                [
                    'Content-Type' => 'application/json'
                ],
                json_encode($this->ffmpeg->getStatus())
            );

        } else {
            $stream = new \React\Stream\ThroughStream();

            $this->ffmpeg->getStream()->on('data', $w = function ($chunk) use ($stream) {

                if ($stream->isWritable()) {
                    $stream->write($chunk);
                } else {
                    $stream->end();
                }

            });

            $stream->on('close', function () use ($w, $count) {

                $this->logger->write('info', 'response stream close ' . $count);
                $this->ffmpeg->getStream()->removeListener('data', $w);

            });

            return new \React\Http\Response(
                200,
                [
                    'Accept-Ranges' => 'bytes',
                    'Connection' => 'keep-alive',
                    'Content-Type' => 'multipart/x-mixed-replace;boundary=ffmpeg'
                ],
                $stream
            );
        }
    }
}