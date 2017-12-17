<?php

namespace SlimX\Exceptions;

use SlimX\Models\Error;

class ErrorCodeNotFoundException extends \Exception
{
    public function __construct(int $code)
    {
        parent::__construct();
        $this->message = "Code $code is not registered";
    }
}
