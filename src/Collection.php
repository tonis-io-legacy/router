<?php
namespace Tonis\Router;

use Psr\Http\Message\ServerRequestInterface;
use Tonis\Router\Exception\RouteExistsException;

final class Collection
{
    /** @var Match|null */
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
     * @return null|Match
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
        } elseif (!isset($this->routes[$name])) {
            $this->routes[$name] = $route;
        } else {
            throw new RouteExistsException($name);
        }
        return $route;
    }

    /**
     * @param ServerRequestInterface $request
     * @return null|Match
     */
    public function match(ServerRequestInterface $request)
    {
        foreach ($this->routes as $route) {
            $match = $this->matchRoute($request, $route);

            if ($match instanceof Match) {
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
     * @return $this
     */
    private function addWithMethod($path, $handler, $method)
    {
        return $this->add($path, $handler)->methods([$method]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Route $route
     * @return bool
     */
    private function matchRoute(ServerRequestInterface $request, Route $route)
    {
        $match = new Match($route);
        foreach ($this->rules as $rule) {
            if (!$rule($request, $match)) {
                return false;
            }
        }
        return $match;
    }
}
