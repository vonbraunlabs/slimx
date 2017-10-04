# SlimX - Slim Extended

Provides a thin layer over PHP Slim Microframework, to provide eXtra features.

## Status

[![Build Status](https://travis-ci.org/vonbraunlabs/slimx.png)](https://travis-ci.org/vonbraunlabs/slimx)

[![Code Climate](https://codeclimate.com/github/vonbraunlabs/slimx.png)](https://codeclimate.com/github/vonbraunlabs/slimx)

## Install

Install the package using composer:

```bash
composer require vonbraunlabs/slimx
```

## SlimX\Controllers\Action

The OO representation of an Action, on the MVC context. Enables HTTP request
header routing. While loading the routes, an array of callables can be provided,
instead of just the callable. If an array is provided, the keys will be used to
determine the `Accept` header:

```php
$entrypoints['testGet'] = new Action(
    'GET',
    '/test,
    [
        'application/vnd.vbl.slimx.v1+json' => function ($request, $response, $args) {
            $response->write("{'api-version': 'v1'}");

            return $response;
        },
        'application/vnd.vbl.slimx.v2+json' => function ($request, $response, $args) {
            $response->write("{'api-version': 'v2'}");

            return $response;
        }
    ]
);
```

## SlimX\Controllers\AbstractController

Slim controllers may extend the AbstractController, provided by the SlimX. By
extending the AbstractController, your controller must extend the loadActions
method, that is responsible for loading all Action objects.

Use the `pushEntrypoint` to register the Action objects into Slim. Moreover, the
constructior will assign `\Slim\App` and the container to the attributes `app`
and `container` respectively.

Here is a example of a controller class using SlimX's AbstractController:

```php
<?php

namespace My\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SlimX\Controllers\AbstractController;
use SlimX\Controllers\Action;

class DefaultController extends AbstractController
{
    /**
     * Implements the abstract method loadAction defined on AbstractController.
     * This method is called when it is time to load the actions into the
     * \Slim\App object.
     */
    public function loadActions()
    {
        $this->pushEntrypoint(new Action(
            'GET',
            '/publickey',
            ['application/vnd.my.project.v1+json' => [$this, 'getBooksAction']],
            [$this, 'handleApiVersionNotSpecified']
        ));
    }

    /**
     * Returns a list of books. In this example method an empty array.
     *
     * @param RequestInterface $request The HTTP request
     * @param ResponseInterface $response The generated HTTP response
     * @param array $args Optional arguments
     *
     * @return ResponseInterface Response processed by the Action
     */
    public function getBooksAction(
        RequestInterface $request,
        ResponseInterface $response,
        array $args
    ) {
        $response->write(json_encode([]));
        return $response;
    }

    /**
     * To be used as a callback by SlimX, handling requests that doesn't
     * provide a valid API version.
     *
     * @param $response ResponseInterface object
     * @return Returns the response, with status code and body set.
     */
    public function handleApiVersionNotSpecified(ResponseInterface $response)
    {
        return $this->container->get('error')->handle($response, 1000);
    }
}
```

## SlimX\Models\Error

When designing an API, there is a number of ways an endpoint can be misused or,
even if every parameter is OK, there is usually scenarios where an error must be
returned to the user. The `Error` class helps maintaining consistency on error
messages. To use it, assign the error list, following the example bellow:

```php
$error = new Error();
$error->setCodeList([
    1000 => [
        'status' => 404',
        'message' => 'User info not found'
    ],
    1001 => [
        'status' => 406',
        'message' => 'You must specify API version'
    ],
]);
```

Afterwards, you can use the method `handle`, providing the
`ResponseInterface` and the error code:

```php
return $error->handle($response, 1000);
```

It will fill the $response object with the right http status code and the JSON
message that show the error code and message:

```json
{"code": 1000, "message": "User info not found"}
``` 
