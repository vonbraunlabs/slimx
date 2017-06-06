<?php

namespace SlimX\Models;

use Psr\Http\Message\ResponseInterface;
use SlimX\Exceptions\ErrorCodeNotFoundException;

class Error
{
    protected $codeList;

    public function handle(ResponseInterface $response, int $code)
    {
        if (isset($this->codeList[$code]) &&
            isset($this->codeList[$code]['status']) &&
            isset($this->codeList[$code]['message'])
        ) {
            $node = $this->codeList[$code];
            $response = $response->withStatus($node['status']);
            $response->getBody()->write(json_encode(['code' => $code, 'message' => $node['message']]));

            return $response;
        } else {
            throw new ErrorCodeNotFoundException($code);
        }
    }

    public function setCodeList(array $codeList)
    {
        $this->codeList = $codeList;
    }
}
