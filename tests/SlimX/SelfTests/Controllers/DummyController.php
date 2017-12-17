<?php

namespace SlimX\SelfTests\Controllers;

use SlimX\Controllers\AbstractController;
use SlimX\Controllers\Action;
use Slim\Http\Request;
use Slim\Http\Response;

class DummyController extends AbstractController
{
    public function loadActions()
    {
        $this->pushEntrypoint(new Action(
            'GET',
            '/',
            [
                'application/vnd.vbl.v1+json' => [$this, 'rootV1Action'],
                'application/vnd.vbl.v2+json' => [$this, 'rootV2Action']
            ]
        ));

        $this->pushEntrypoint(new Action(
            'GET',
            '/versionless',
            [$this, 'versionlessAction']
        ));
    }

    public function rootV1Action(Request $request, Response $response, array $args)
    {
        return $response->write("{'version': 'v1'}");
    }

    public function rootV2Action(Request $request, Response $response, array $args)
    {
        return $response->write("{'version': 'v2'}");
    }

    public function versionlessAction(Request $request, Response $response, array $args)
    {
        return $response->write("{'version': null}");
    }
}
