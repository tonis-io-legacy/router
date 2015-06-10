<?php
namespace Tonis\Router;

use Psr\Http\Message\RequestInterface;
use Tonis\Router\Exception\RouteExistsException;

final class RouteCollection
{
    /** @var RouteMatch|null */
    private $lastMatch;
    /** @var Rule\RuleInterface[] */
    private $rules;
    /** @var Route[] */
    private $routes = [];

    public function __construct()
    {
        $this->rules = [
            new Rule\Secure,
            new Rule\Method(),
            new Rule\Path()
        ];
    }

    /**
     * @return null|Route
     */
    public function getLastMatch()
    {
        return $this->lastMatch;
    }

    /**
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param string $path
     * @param null $handler
     * @param string $name
     * @return $this
     */
    public function get($path, $handler, $name = null)
    {
        return $this->addWithMethod($path, $handler, 'GET', $name);
    }

    /**
     * @param string $path
     * @param null $handler
     * @param string $name
     * @return $this
     */
    public function put($path, $handler, $name = null)
    {
        return $this->addWithMethod($path, $handler, 'PUT', $name);
    }

    /**
     * @param string $path
     * @param null $handler
     * @param string $name
     * @return $this
     */
    public function post($path, $handler, $name = null)
    {
        return $this->addWithMethod($path, $handler, 'POST', $name);
    }

    /**
     * @param string $path
     * @param null $handler
     * @param string $name
     * @return $this
     */
    public function patch($path, $handler, $name = null)
    {
        return $this->addWithMethod($path, $handler, 'PATCH', $name);
    }

    /**
     * @param string $path
     * @param null $handler
     * @param string $name
     * @return $this
     */
    public function delete($path, $handler, $name = null)
    {
        return $this->addWithMethod($path, $handler, 'DELETE', $name);
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
        } elseif (!isset($this->routes[$name])) {
            $this->routes[$name] = $route;
        } else {
            throw new RouteExistsException($name);
        }
        return $route;
    }

    /**
     * @param RequestInterface $request
     * @return null|RouteMatch
     */
    public function match(RequestInterface $request)
    {
        foreach ($this->routes as $route) {
            $match = $this->matchRoute($request, $route);

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
        if (!isset($this->routes[$name])) {
            throw new Exception\RouteDoesNotExistException($name);
        }

        $route = $this->routes[$name];
        foreach ($route->getTokens() as $token) {
            list($name, $optional) = $token;
            if (!$optional && !isset($params[$name])) {
                throw new Exception\MissingParameterException($route->getPath(), $name);
            }
        }
        $replace = function ($matches) use ($params) {
            if (isset($params[$matches[2]])) {
                return $matches[1] . $params[$matches[2]];
            }
            return '';
        };
        return preg_replace_callback('@{([^A-Za-z]*)([A-Za-z]+)[?]?(?::[^}]+)?}@', $replace, $route->getPath());
    }

    /**
     * @param string $path
     * @param mixed $handler
     * @param string $method
     * @param string $name
     * @return $this
     */
    private function addWithMethod($path, $handler, $method, $name = null)
    {
        return $this->add($path, $handler, $name)->methods([$method]);
    }

    /**
     * @param RequestInterface $request
     * @param Route $route
     * @return bool
     */
    private function matchRoute(RequestInterface $request, Route $route)
    {
        $match = new RouteMatch($route);
        foreach ($this->rules as $rule) {
            if (!$rule($request, $match)) {
                return false;
            }
        }
        return $match;
    }
}
