# Tonis\Router

[![Build Status](https://scrutinizer-ci.com/g/tonis-io/router/badges/build.png?b=master)](https://scrutinizer-ci.com/g/tonis-io/router/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/tonis-io/router/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tonis-io/router/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tonis-io/router/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tonis-io/router/?branch=master)

## Installation
Tonis\Router can be installed using composer which will setup any autoloading for you.

`composer require tonis-io/router`

Additionally, you can download or clone the repository and setup your own autoloading.

## Adding routes

```php
use Tonis\Router\Router;

$router = new Router();

// Basic route with name
$router->add('foo', '/foo');
// matches /foo

// Basic route with no name
$router->add(null, '/foo');
// matches /foo

// Route with tokens
$router->add('foo', '/foo/{name}');
// matches /foo/bar

// Router with optional tokens
$router->add('foo', '/foo{/name?}');
// matches /foo or /foo/bar

// Router with tokens and constraints
$router->add('foo', '/foo/{id:\d+}-{slug}');
// matches /foo/1-bar but not /foo/baz-bar

// The kitchen sink
$router->add('foo', '/foo/{id:\d+}{-slug?:[a-zA-Z-_]+}');
// matches /foo/1, /foo/1-bar, /foo/1-BaR
// does not match /foo/1-2
```

## Assembling named routes to url's

```php
use Tonis\Router\Router;

$router = new Router();
$router->add('foo', '/foo');

// outputs '/foo'
echo $router->assemble('foo');

$router->add('foo', '/foo/{id:\d+}{-slug?:[a-zA-Z-_]+}');

// outputs '/foo/1'
echo $router->assemble('foo', ['id' => 1]);

// outputs '/foo/1-bar'
echo $router->assemble('foo', ['id' => 1, 'slug' => 'bar']);
```

## Matching routes

```php
use Tonis\Router\Router;

$router = new Router();
$router->add('foo', '/foo');

// result is NULL
echo $router->match('/bar');

// result is an instance of Tonis\Router\Match
$match = $router->match('/foo');

// output is 'foo'
echo $match->getName();

$router->add('bar', '/bar/{id}');

// result is an instance of Tonis\Router\Match
$match = $router->match('/bar/1');

// output is 'bar'
echo $match->getName();

// output is '1'
echo $match->get('id');

// you can also have defaults for params that may not exist (output is 'foo')
echo $match->get('does-not-exist', 'foo');
```

## Default route parameters

```php
use Tonis\Router\Router;

$router = new Router();
$router->add('foo', '/foo{/id?}', ['defaults' => ['id' => 1, 'controller' => 'foo-controller']]);

$match = $router->match('/foo/1234');

// output is '1234'
echo $match->get('id');

$match = $router->match('/foo');

// output is '1'
echo $match->get('id');

// output is 'foo-controller'
echo $match->get('controller');
