<?php
namespace Tonis\Router\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Tonis\Router\Router;

final class RouteExtension implements ExtensionInterface
{
    /** @var Router */
    private $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('url', [$this, 'urlFunction']);
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    public function urlFunction($name, array $params = [])
    {
        return $this->router->assemble($name, $params);
    }
}
