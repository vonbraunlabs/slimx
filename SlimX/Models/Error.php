<?php

namespace SlimX\Models;

use Psr\Http\Message\ResponseInterface;
use SlimX\Exceptions\ErrorCodeNotFoundException;

class Error
{
    protected $codeList;
    protected $headerList;

    public function __construct()
    {
        $this->codeList = [];
        $this->headerList = [];
    }

    public function handle(
        ResponseInterface $response,
        int $code,
        string $customMessage = ''
    ) {
        if (isset($this->codeList[$code]) &&
            isset($this->codeList[$code]['status']) &&
            isset($this->codeList[$code]['message'])
        ) {
            $node = $this->codeList[$code];
            $response = $response->withStatus($node['status']);
            $json = ['code' => $code, 'message' => $node['message']];
            if (!empty($customMessage)) {
                $json['customMessage'] = $customMessage;
            }
            $response->getBody()->write(json_encode($json));
            foreach ($this->headerList as $key => $value) {
                $response = $response->withAddedHeader($key, $value);
            }

            return $response;
        } else {
            throw new ErrorCodeNotFoundException($code);
        }
    }

    /**
     * Get the node with the corresponding code.
     *
     * @param int $code Code number.
     * @return array Corresponding element in the array.
     */
    public function getNode(int $code): array
    {
        if (isset($this->codeList[$code])) {
            return $this->codeList[$code];
        } else {
            throw new ErrorCodeNotFoundException($code);
        }
    }

    public function setCodeList(array $codeList)
    {
        $this->codeList = $codeList;
    }

    public function setHeaderList(array $headerList)
    {
        $this->headerList = $headerList;
    }
}
