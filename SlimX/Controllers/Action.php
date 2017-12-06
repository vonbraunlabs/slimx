<?php

namespace SlimX\Controllers;

class Action
{
    private $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    protected $method;
    protected $pattern;
    protected $callable;

    /**
     * Organize the action.
     *
     * @param $method An HTTP method supported by Slim.
     * @param $pattern URI to route the request.
     * @param $callable Either an anonymous function or an array with keys
     *  matching the HTTP_ACCEPT and values being the anonymous function to be
     *  called.
     * @param $errorCallable Optionally, a callback function may be given, to be
     *  called when ACCEPT header doesn't match any of the expected.
     */
    public function __construct(string $method, string $pattern, $callable, ?callable $errorCallable = null)
    {
        if (!in_array($method, $this->allowedMethods)) {
            throw new \Exception(
                "Http method $method is not allowed. List of allowed methods: " .
                implode(', ', $this->allowedMethods)
            );
        }
        $this->method = $method;
        $this->pattern = $pattern;
        if (is_callable($callable)) {
            $this->callable = $callable;
        } else {
            $this->callable = function ($request, $response, $args) use ($callable, $errorCallable)
            {
                $headers = $request->getHeaders();
                if (isset($headers['HTTP_ACCEPT'])) {
                    foreach ($headers['HTTP_ACCEPT'] as $accept) {
                        if (isset($callable[$accept])) {
                            return $callable[$accept]($request, $response, $args);
                        }
                    }
                }

                if (null !== $errorCallable) {
                    return $errorCallable($response);
                } else {
                    $response = $response->withStatus(406);
                    $response->write('API version not present or not accepted');

                    return $response;
                }
            };
        }
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function getCallable()
    {
        return $this->callable;
    }
}
