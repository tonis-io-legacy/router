<?php
namespace Tonis\Router\Twig;

use Tonis\Router\RouteCollection;
use Tonis\Router\Router;

final class RouteFunction extends \Twig_SimpleFunction
{
    /** @var RouteCollection */
    private $router;

    /**
     * @param Router $router
     * @param string $name
     */
    public function __construct(Router $router, $name)
    {
        $this->router = $router;
        parent::__construct($name, [$this, '__invoke']);
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    public function __invoke($name, array $params = [])
    {
        return $this->router->assemble($name, $params);
    }
}
