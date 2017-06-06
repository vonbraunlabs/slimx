# SlimX - Slim Extended

Provides a thin layer over PHP Slim Microframework, to provide eXtra features.

## Status

[![Build Status](https://travis-ci.org/vonbraunlabs/slimx.png)](https://travis-ci.org/vonbraunlabs/slimx)

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

## SlimX\Models\Error

When designing an API, there is a number of ways an endpoint can be misused or,
even if every parameter is OK, there is usually scenarios where an error must be
returned to the user. The `Error` class helps maintaining consistency on error
messages. To use it, assign the error list, following the example bellow:

```php
\SlimX\Models\Error::$codeList = [
    1000 => [
        'status' => 404',
        'message' => 'User info not found'
    ],
    1001 => [
        'status' => 406',
        'message' => 'You must specify API version'
    ],
];
```

Afterwards, you can use the static method `handle`, providing the
`ResponseInterface` and the error code:

```php
return \SlimX\Models\Error::handle($response, 1000);
```

It will fill the $response object with the right http status code and the JSON
message that show the error code and message:

```json
{'code': 1000, 'message': 'User info not found'}
``` 
