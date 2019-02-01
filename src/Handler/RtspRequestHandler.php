<?php

namespace Handler;


use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use Service\FFmpeg;
use Service\Logger;

class RtspRequestHandler
{
    /**
     * @var \Service\FFmpeg
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
     * RtspRequestHandler constructor.
     *
     * @param LoopInterface $loop
     * @param Logger $logger
     * @param FFmpeg $ffmpeg
     */
    public function __construct(LoopInterface $loop, Logger $logger, FFmpeg $ffmpeg)
    {
        $this->loop = $loop;
        $this->logger = $logger;
        $this->ffmpeg = $ffmpeg;
    }

    public function handleRequest(ServerRequestInterface $request)
    {
        $this->logger->write('info', 'request');

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

            $this->ffmpeg->stdout->on('data', $w = function ($chunk) use ($stream) {

                if ($stream->isWritable()) {
                    $stream->write($chunk);
                } else {
                    $stream->end();
                }

            });

            $stream->on('close', function () use ($w) {
                $this->ffmpeg->stdout->removeListener('data', $w);
            });

            $this->ffmpeg->stdout->on('close', function () use ($stream) {
                $stream->end();
            });

            $this->ffmpeg->on('exit', function () use ($stream) {
                $stream->end();
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