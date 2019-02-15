<?php
/**
 * Created by PhpStorm.
 * User: inisire
 * Date: 14.02.19
 * Time: 10:19
 */

namespace Stream;


use Evenement\EventEmitter;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableStreamInterface;
use React\Stream\Util;
use React\Stream\WritableStreamInterface;

class ReadableStream extends EventEmitter implements ReadableStreamInterface
{
    /**
     * @var ReadableStreamInterface
     */
    private $stream;

    private $paused = true;

    private $pausedAt = 0;

    private $buffer = "";

    private $frame;

    /**
     * @var LoopInterface
     */
    private $loop;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param $data
     *
     * @return string
     */
    private function extractFrame($data)
    {
//        $data = str_replace("\r", "/r", $data);
//        $data = str_replace("\n", "/n", $data);

        $this->buffer .= $data;

        $start = strpos($this->buffer, "--ffmpeg\r\n");
        $end = strpos($this->buffer, "\r\n", $start);

        if ($start && $end) {

            echo 'start=' . $start . PHP_EOL;
            echo 'end=' . $end . PHP_EOL;

            $frame = substr($this->buffer, $start, $end - $start + 1);
            $this->buffer = substr($this->buffer, $end + 1);

            return $frame;
        }

        return "";
    }

    /**
     * ReadableStream constructor.
     *
     * @param ReadableStreamInterface $stream
     */
    public function __construct(ReadableStreamInterface $stream, LoopInterface $loop, LoggerInterface $logger)
    {
        $this->stream = $stream;
        $this->loop = $loop;
        $this->logger = $logger;

        $bufferTimer = $this->loop->addPeriodicTimer(60, function () {

            $this->logger->info(sprintf('buffer_size=%s', strlen($this->buffer)));

        });

        $pauseTimer = $this->loop->addPeriodicTimer(5, function () {

            if ($this->pausedAt && time() - $this->pausedAt > 5) {
                $this->logger->error('Stream closed due to inactive');
                $this->close();
            }

        });

        $this->stream->on('data', $listener = function ($data) {

            $this->buffer .= $data;

            if (!$this->paused) {

                $this->emit('data', [$this->buffer]);
                $this->buffer = "";

            }

            // If buffer > 5 MB
            if (strlen($this->buffer) > 5 * 1024 * 1024) {
                $this->logger->error('Clear buffer due to overflow');
                $this->buffer = "";
            }


        });

        $this->on('close', function () use ($listener, $bufferTimer, $pauseTimer) {

            $this->buffer = "";
            $this->stream->removeListener('data', $listener);
            $this->loop->cancelTimer($bufferTimer);
            $this->loop->cancelTimer($pauseTimer);

        });

        $this->resume();
    }

    /**
     * @inheritDoc
     */
    public function isReadable()
    {
        return $this->stream->isReadable();
    }

    /**
     * @inheritDoc
     */
    public function pause()
    {
        $this->paused = true;
        $this->pausedAt = time();

    }

    /**
     * @inheritDoc
     */
    public function resume()
    {
        $this->paused = false;
    }

    /**
     * @inheritDoc
     */
    public function pipe(WritableStreamInterface $dest, array $options = array())
    {
        return Util::pipe($this, $dest, $options);
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        $this->emit('end');
        $this->emit('close');
        $this->removeAllListeners();
    }

}