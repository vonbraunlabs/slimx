<?php

namespace SlimX\Models;

use Psr\Http\Message\ResponseInterface;
use SlimX\Exceptions\ErrorCodeNotFoundException;

class Error
{
    public static $codeList;

    public static function handle(ResponseInterface $response, int $code)
    {
        if (isset(self::$codeList[$code]) &&
            isset(self::$codeList[$code]['status']) &&
            isset(self::$codeList[$code]['message'])
        ) {
            $node = self::$codeList[$code];
            $response = $response->withStatus($node['status']);
            $response->getBody()->write(json_encode(['code' => $code, 'message' => $node['message']]));

            return $response;
        } else {
            throw new ErrorCodeNotFoundException($code);
        }
    }
}
