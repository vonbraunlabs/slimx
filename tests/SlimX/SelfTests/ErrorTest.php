<?php

namespace SlimX\SelfTests;

use PHPUnit\Framework\TestCase;
use SlimX\Models\Error;
use GuzzleHttp\Psr7\Response;
use SlimX\Exceptions\ErrorCodeNotFoundException;

class ErrorTest extends TestCase
{
    protected $error;

    public function setup()
    {
        $this->error = new Error();
        $this->error->setCodeList([
            '1000' => [
                'status' => 404,
                'message' => 'Page not found'
            ]
        ]);
    }

    public function testSuccess()
    {
        $response = new Response(200);
        $response = $this->error->handle($response, 1000);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(
            json_encode(['code' => 1000, 'message' => 'Page not found']),
            (string) $response->getBody()
        );
    }

    public function testMissingCode()
    {
        $response = new Response(200);
        $this->expectException(ErrorCodeNotFoundException::class);
        $response = $this->error->handle($response, 1001);
    }

    public function testGetNodeSuccess()
    {
        $node = $this->error->getNode(1000);
        $this->assertInternalType('array', $node);
        $this->assertNotEmpty($node);
        $this->assertArrayHasKey('status', $node);
        $this->assertArrayHasKey('message', $node);
    }

    public function testGetNodeError()
    {
        $this->expectException(ErrorCodeNotFoundException::class);
        $this->error->getNode(1001);
    }
}
