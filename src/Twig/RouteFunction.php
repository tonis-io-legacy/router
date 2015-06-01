<?php
namespace Tonis\Router\Twig;

use Tonis\Router\Collection;

class RouteFunction extends \Twig_SimpleFunction
{
    /**
     * @var \Tonis\Router\Collection
     */
    private $router;

    /**
     * @param Collection $router
     * @param string $name
     */
    public function __construct(Collection $router, $name)
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
