<?php
namespace Tonis\Router;

use Psr\Http\Message\RequestInterface;
use Tonis\Router\Exception\RouteExistsException;

final class RouteCollection
{
    /** @var Route[] */
    private $namedRoutes = [];
    /** @var Route[] */
    private $routes = [];
    /** @var RouteMatch */
    private $lastMatch;

    /**
     * @param string $path
     * @param null $handler
     * @return $this
     */
    public function get($path, $handler)
    {
        return $this->addWithMethod($path, $handler, 'GET');
    }

    /**
     * @param string $path
     * @param null $handler
     * @return $this
     */
    public function put($path, $handler)
    {
        return $this->addWithMethod($path, $handler, 'PUT');
    }

    /**
     * @param string $path
     * @param null $handler
     * @return $this
     */
    public function post($path, $handler)
    {
        return $this->addWithMethod($path, $handler, 'POST');
    }

    /**
     * @param string $path
     * @param null $handler
     * @return $this
     */
    public function patch($path, $handler)
    {
        return $this->addWithMethod($path, $handler, 'PATCH');
    }

    /**
     * @param string $path
     * @param null $handler
     * @return $this
     */
    public function delete($path, $handler)
    {
        return $this->addWithMethod($path, $handler, 'DELETE');
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string|null $name
     * @return Route
     * @throws Exception\RouteExistsException
     */
    public function add($path, $handler, $name = null)
    {
        $route = new Route($path, $handler);

        if (null === $name) {
            $this->routes[] = $route;
        } else {
            if (isset($this->namedRoutes[$name])) {
                throw new RouteExistsException($name);
            }
            $this->namedRoutes[$name] = $route;
        }

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
            $match = $route->match($request);
            
            if ($match instanceof RouteMatch) {
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
        if (!isset($this->namedRoutes[$name])) {
            throw new Exception\RouteDoesNotExistException($name);
        }

        return $this->namedRoutes[$name]->assemble($params);
    }

    /**
     * @return RouteMatch
     */
    public function getLastMatch()
    {
        return $this->lastMatch;
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $method
     * @return $this
     */
    private function addWithMethod($path, $handler, $method)
    {
        return $this->add($path, $handler)->withMethods([$method]);
    }
}
