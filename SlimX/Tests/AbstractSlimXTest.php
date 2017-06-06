<?php

namespace SlimX\Tests;

use PHPUnit\Framework\TestCase;

abstract class AbstractSlimXTest extends TestCase
{
    protected $app;
    protected $client;
    protected $method;
    protected $endpoint;
    protected $requestHeaders = ['HTTP_ACCEPT' => 'application/vnd.v1+json'];

    protected abstract function getValidData() : array;

    public abstract function getSlimInstance();

    public function setup()
    {
        $this->app = $this->getSlimInstance();
        $this->client = new WebTestClient($this->app);
        parent::setup();
    }

    /**
     * Valid requests should produce a 2xx http response code.
     */
    public function testValidRequest()
    {
        $this->client->{$this->method}(
            $this->endpoint,
            $this->getValidData(),
            $this->requestHeaders
        );

        $httpCode = $this->client->getResponse()->getStatusCode();
        $body = $this->client->getResponse()->getBody();
        $this->assertTrue(
            200 <= $httpCode && $httpCode < 300,
            "Returned code $httpCode is not 2xx. Body: " . $body
        );
    }
}
