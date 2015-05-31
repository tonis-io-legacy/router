<?php
namespace Tonis\Router;

final class RouteMatch
{
    /** @var Route */
    private $route;
    /** @var array */
    private $params;

    /**
     * @param Route $route
     * @param string[] $params
     */
    public function __construct(Route $route, array $params = [])
    {
        $this->route = $route;
        $this->params = $params;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return \Tonis\Router\Route
     */
    public function getRoute()
    {
        return $this->route;
    }
}
