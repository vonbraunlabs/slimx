<?php

namespace SlimX\Controllers;

abstract class AbstractController
{
    private $entrypoints;

    protected $app;
    protected $container;

    public function __construct(\Slim\App $app)
    {
        $this->app = $app;
        $this->container = $app->getContainer();
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
