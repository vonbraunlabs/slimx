<?php

namespace SlimX\SelfTests\Controllers;

use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Container;
use SlimX\Controllers\AbstractController;
use SlimX\Controllers\Action;
use SlimX\Tests\WebTestClient;

class AbstractControllerTest extends TestCase
{
    protected $client;

    protected function setUp()
    {
        $containter = new Container([]);
        $app = new App($containter);
        $controller = new DummyController($app);
        $controller->loadActions();

        $this->client = new WebTestClient($app);
    }

    public function testRequestToV1()
    {
        $this->client->get('/', [], ['HTTP_ACCEPT' => 'application/vnd.vbl.v1+json']);
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("{'version': 'v1'}", $response->getBody());
    }

    public function testRequestToV2()
    {
        $this->client->get('/', [], ['HTTP_ACCEPT' => 'application/vnd.vbl.v2+json']);
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("{'version': 'v2'}", $response->getBody());
    }

    public function testRequestWithMissingAccept()
    {
        $this->client->get('/');
        $response = $this->client->getResponse();
        $this->assertEquals(406, $response->getStatusCode());
        $this->assertEquals(
            'API version not present or not accepted',
            $response->getBody()
        );
    }

    public function getRequestVersionlessData()
    {
        return [
            [[]],
            [['HTTP_ACCEPT' => 'application/vnd.custom+json']],
        ];
    }

    /**
     * @dataProvider getRequestVersionlessData
     */
    public function testRequestVersionalessAction($header)
    {
        $this->client->get('/versionless', [], $header);
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("{'version': null}", $response->getBody());
    }
}
