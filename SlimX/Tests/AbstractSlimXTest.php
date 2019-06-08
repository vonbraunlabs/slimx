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
     * @param ResponseInterface $response Request's response
     * @param int $code Expected error code
     * @param ?int $codeMax If specified, $code and $codeMax will act as minimum
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

    protected function assertResponseCode(int $min, ?int $max = null, ?string $message = null)
    {
        $max = null === $max ? $min : $max;
        $httpCode = $this->client->getResponse()->getStatusCode();
        $body = $this->client->getResponse()->getBody();

        if ($message !== null) {
            $message = ". Custom message: $message";
        }
        $this->assertTrue(
            $min <= $httpCode && $httpCode <= $max,
            "Returned code $httpCode is not between $min and $max. Body: " .
            $body . $message
        );
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
        $this->assertResponseCode(200, 299);
    }

    public function testCorsOptions()
    {
        $this->client->options($this->endpoint, []);

        $this->assertResponseCode(200, 299);
        $response = $this->client->getResponse();
    }

    /**
     * Get full path of given file. Look for file on current dir, and
     * recursively on its parent dir, until file is found.
     *
     * @param string $file File name.
     *
     * @return string Full name of the file.
     */
    protected function getFullPath(string $file): string
    {
        for ($i = 0;
            !file_exists(
                $fullPath = __DIR__ . '/' . str_repeat('../', $i) . $file
            ) && $i < 100;
            $i++) {
        }
        return $fullPath;
    }
}
