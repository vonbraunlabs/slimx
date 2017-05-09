# SlimX - Slim Extended

Provides a thin layer over PHP Slim Microframework, to provide eXtra features.

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
