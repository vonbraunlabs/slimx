<?php

namespace SlimX\Exceptions;

class ErrorCodeException extends \Exception
{
    public function __construct(int $code)
    {
        parent::__construct('', $code);
    }
}
