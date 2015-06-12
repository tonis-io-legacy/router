<?php

namespace Tonis\Router\Twig;

use Tonis\Router\Router;

/**
 * @coversDefaultClass \Tonis\Router\Twig\RouteFunction
 */
class RouteFunctionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $router = new Router();
        $router->add('/foo', 'handler', 'foo');

        $f = new RouteFunction($router, 'foo');
        $this->assertSame($router->assemble('foo'), $f('foo'));
    }
}
