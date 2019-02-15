<?php

namespace Service;

class ExceptionDecorator
{
    public static function decorate(\Throwable $e) : string
    {
        return sprintf(
            'Exception %s with message "%s" in %s:%s',
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
    }
}