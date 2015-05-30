<?php

namespace Tonis\Router;

use Psr\Http\Message\RequestInterface;

final class RouteCollection
{
    /** @var Route[] */
    private $routes = [];
    /** @var RouteMatch */
    private $lastMatch;

    /**
     * @return \ArrayObject
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param string $path
     * @param null $handler
     * @return $this
     */
    public function get($path, $handler)
    {
        return $this->add($path, $handler)->methods(['GET']);
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @return Route
     * @throws Exception\RouteExistsException
     */
    public function add($path, $handler)
    {
        $route = new Route($path, $handler);
        $this->routes[] = $route;

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
        if (!isset($this->routes[$name])) {
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
