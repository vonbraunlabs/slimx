<?php

namespace SlimX\Controllers;

class Action
{
    private $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH'];
    protected $method;
    protected $pattern;
    protected $callable;

    public function __construct(string $method, string $pattern, callable $callable)
    {
        if (!in_array($method, $this->allowedMethods)) {
            throw new \Exception(
                "Http method $method is not allowed. List of allowed methods: " .
                implode(', ', $this->allowedMethods)
            );
        }
        $this->method = $method;
        $this->pattern = $pattern;
        $this->callable = $callable;
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
