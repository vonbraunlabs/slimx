<?php

namespace SlimX\Exceptions;

use SlimX\Models\Error;

class ErrorCodeNotFoundException extends \Exception
{
    public function __construct(int $code)
    {
        $this->message = "Code $code is not registered: " . print_r(Error::$codeList, true);
    }
}
