<?php

namespace SlimX\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractSlimXTest extends TestCase
{
    protected $app;
    protected $client;
    protected $method;
    protected $endpoint;
    protected $requestHeaders = ['HTTP_ACCEPT' => 'application/vnd.v1+json'];

    abstract protected function getValidData() : array;

    /**
     * Asserts if given response respects what is expected from the respective Error obj.
     *
     * @param $response Request's response
     * @param $code Expected error code
     * @param $codeMax If specified, $code and $codeMax will act as minimum
     * expected error code and maximum expected error code, respectively.
     * @return void
     */
    protected function assertError(ResponseInterface $response, int $code, ?int $codeMax = null)
    {
        $body = (string) $response->getBody();
        $json = json_decode($body);
        $this->assertNotNull($json, "Returned body is not valid json: " . $body);
        $this->assertInstanceOf('stdClass', $json);
        $this->assertNotEmpty($json);
        $this->assertTrue(isset($json->code), "Code not present: " . $body);
        $this->assertTrue(isset($json->message), "Message not present: " . $body);
        $error = $this->app->getContainer()->get('error');
        if (null !== $codeMax) {
            $this->assertTrue(
                $code <= $json->code && $json->code <= $codeMax,
                "Code {$json->code} is not within boundaries min $code, max $codeMax"
            );
        } else {
            $this->assertEquals($code, $json->code, "Expecting code $code but received JSON $body");
        }
        $node = $error->getNode($json->code);
        $this->assertEquals($node['status'], $response->getStatusCode());
        $this->assertEquals($node['message'], $json->message);
    }

    abstract public function getSlimInstance();

    protected function setUp()
    {
        $this->app = $this->getSlimInstance();
        $this->client = new WebTestClient($this->app);
        parent::setUp();
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
