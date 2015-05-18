<?php

namespace Tonis\Router;

use Psr\Http\Message\RequestInterface;

final class RouteCollection
{
    /** @var \ArrayObject */
    private $routes;
    /** @var RouteMatch */
    private $lastMatch;

    public function __construct()
    {
        $this->routes = new \ArrayObject();
    }

    /**
     * @return \ArrayObject
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param string $name
     * @param string $path
     * @param null $handler
     * @return $this
     */
    public function get($name, $path, $handler)
    {
        return $this->add($name, $path, $handler)->methods(['GET']);
    }

    /**
     * @param string|null $name
     * @param string $path
     * @param mixed $handler
     * @return Route
     * @throws Exception\RouteExistsException
     */
    public function add($name, $path, $handler)
    {
        if (null !== $name && $this->routes->offsetExists($name)) {
            throw new Exception\RouteExistsException($name);
        }

        $route = new Route($name, $path, $handler);
        $this->routes[$name] = $route;

        return $route;
    }

    /**
     * @param RequestInterface $request
     * @return RouteMatch|null
     */
    public function match(RequestInterface $request)
    {
        /** @var Route $route */
        foreach ($this->routes as $route) {
            if ($match = $route->match($request)) {
                $this->lastMatch = $match;
                return $match;
            }
        }
        return null;
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     * @throws Exception\RouteDoesNotExistException
     */
    public function assemble($name, array $params = [])
    {
        if (!$this->routes->offsetExists($name)) {
            throw new Exception\RouteDoesNotExistException($name);
        }

        /** @var Route $route */
        $route = $this->routes[$name];
        return $route->assemble($params);
    }

    /**
     * @return RouteMatch
     */
    public function getLastMatch()
    {
        return $this->lastMatch;
    }
}
