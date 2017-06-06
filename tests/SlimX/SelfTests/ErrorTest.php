<?php

namespace SlimX\SelfTests;

use PHPUnit\Framework\TestCase;
use SlimX\Models\Error;
use GuzzleHttp\Psr7\Response;
use SlimX\Exceptions\ErrorCodeNotFoundException;

class ErrorTest extends TestCase
{
    public function setup()
    {
        Error::$codeList = [
            '1000' => [
                'status' => 404,
                'message' => 'Page not found'
            ]
        ];
    }

    public function testSuccess()
    {
        $response = new Response(200);
        $response = Error::handle($response, 1000);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(json_encode(['code' => 1000, 'message' => 'Page not found']), (string) $response->getBody());
    }

    public function testMissingCode()
    {
        $response = new Response(200);
        $this->expectException(ErrorCodeNotFoundException::class);
        $response = Error::handle($response, 1001);
    }
}
