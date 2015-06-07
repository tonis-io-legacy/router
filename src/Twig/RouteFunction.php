<?php
namespace Tonis\Router\Twig;

use Tonis\Router\RouteCollection;

final class RouteFunction extends \Twig_SimpleFunction
{
    /** @var RouteCollection */
    private $routes;

    /**
     * @param RouteCollection $routes
     * @param string $name
     */
    public function __construct(RouteCollection $routes, $name)
    {
        $this->routes = $routes;
        parent::__construct($name, [$this, '__invoke']);
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    public function __invoke($name, array $params = [])
    {
        return $this->routes->assemble($name, $params);
    }
}
