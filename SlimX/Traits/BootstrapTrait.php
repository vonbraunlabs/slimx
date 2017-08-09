<?php

namespace SlimX\Traits;

trait BootstrapTrait
{
    protected $app;

    public function __construct()
    {
        $settings = require __DIR__ . '/../../../config/application.php';
        $container = new Container($settings);

        $conf = $container->get('settings')['db'];
        $strConn = "mysql:host={$conf['host']};dbname={$conf['dbname']}";
        if (!R::testConnection()) {
            R::setup($strConn, $conf['user'], $conf['pass']);
            R::ext('xdispense', function ($type) {
                return R::getRedBean()->dispense($type);
            });
        }

        $app = new App($container);

        $dependencyManager = new DependencyManager($app);
        $dependencyManager->loadDependencies();

        $middleware = new Middleware($app);
        $middleware->loadMiddleware();

        $controller = new DefaultController($app);
        $controller->loadActions();

        $this->app = $app;
    }

    public function getApp()
    {
        return $this->app;
    }

}
