<?php

namespace Service;

use Psr\Log\LoggerInterface;

class SafeCallableWrapper
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SafeCallableGenerator constructor.
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param callable $callable
     *
     * @return callable
     */
    public function wrap(callable $callable) : callable
    {
        return function () use ($callable) {

            try {

                call_user_func_array($callable, func_get_args());

            } catch (\Exception $e) {

                if ($this->logger) {
                    $this->logger->error(ExceptionDecorator::decorate($e));
                } else {
                    syslog(LOG_ERR, ExceptionDecorator::decorate($e));
                }

            }
        };
    }
}