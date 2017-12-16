<?php

namespace SlimX\Tests;

/**
 * Based on WebTestClient from
 * git@github.com:there4/slim-unit-testing-example.git
 */

use \Slim\Http\Environment;

/**
 * Mocks the client request and response.
 */
class WebTestClient
{
    private $app;
    private $request;
    private $response;

    /**
     * Abstract way to make a request to SlimPHP, this allows us to mock the
     * slim environment.
     *
     * @param  string $method          Method name.
     * @param  string $path            URI.
     * @param  array  $data            Data request.
     * @param  array  $optionalHeaders Optional headers.
     * @return void.
     */
    private function request(
        string $method,
        string $path,
        $data = array(),
        array $optionalHeaders = array()
    ) {
        // Capture STDOUT
        ob_start();

        $options = array(
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI'      => $path,
        );

        if ($method === 'get') {
            $options['QUERY_STRING'] = http_build_query($data);
        } elseif (is_array($data)) {
            $options['slim.input']   = http_build_query($data);
        } else {
            $options['slim.input']   = $data;
        }

        // Prepare a mock environment
        $env = Environment::mock(array_merge($options));
        $request = \Slim\Http\Request::createFromEnvironment($env);

        foreach ($optionalHeaders as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        if ('get' !== $method && is_array($data)) {
            $request = $request->withParsedBody($data);
        }

        // Execute our app
        $response = new \Slim\Http\Response();
        $this->response = $this->app->process($request, $response);
        $this->request = $request;

        // Return the application output. Also available in `response->body()`
        return ob_get_clean();
    }


    // We support these methods for testing. These are available via
    // `this->get()` and `$this->post()`. This is accomplished with the
    // `__call()` magic method below.
    private $testingMethods = array(
        'get', 'post', 'patch', 'put', 'delete', 'head', 'options'
    );

    /**
     * Set the Slim app.
     *
     * @param \Slim\App $app Slim app instance.
     */
    public function __construct(\Slim\App $app)
    {
        $this->app = $app;
    }

    /**
     * Implement our `get`, `post`, and other http operations, as defined on
     * $testingMethods.
     *
     * @param  string $method    Method being called.
     * @param  array  $arguments List of arguments.
     * @return void.
     */
    public function __call(string $method, array $arguments)
    {
        if (in_array($method, $this->testingMethods)) {
            list($path, $data, $headers) = array_pad($arguments, 3, array());
            return $this->request($method, $path, $data, $headers);
        }
        throw new \BadMethodCallException(
            strtoupper($method) . ' is not supported'
        );
    }

    /**
     * Get the response objects.
     *
     * @return \Slim\Http\Response Response object.
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the request object.
     *
     * @return \Slim\Http\Request Request object.
     */
    public function getRequest()
    {
        return $this->request;
    }
}
