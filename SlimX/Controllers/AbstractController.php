<?php

namespace SlimX\Controllers;

abstract class AbstractController
{
    private $entrypoints;

    protected $app;

    public function __construct(\Slim\App $app)
    {
        $this->app = $app;
    }

    abstract public function loadActions();

    public function pushEntrypoint(Action $action)
    {
        $app = $this->app;
        $this->entrypoints[] = $action;

        $method = $action->getMethod();
        $app->$method($action->getPattern(), $action->getCallable());
    }
}
