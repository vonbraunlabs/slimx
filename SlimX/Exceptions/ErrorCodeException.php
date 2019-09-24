<?php

namespace SlimX\Exceptions;

class ErrorCodeException extends \Exception
{
    public function __construct(int $code, string $message = '')
    {
        parent::__construct($message, $code);
    }
}
