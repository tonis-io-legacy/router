<?php
namespace Tonis\Router;

/**
 * @coversDefaultClass \Tonis\Router\RouteMatch
 */
class RouteMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRoute
     * @covers ::setParams
     * @covers ::getParams
     */
    public function testAllTheThings()
    {
        $route = new Route('/foo', 'handler');
        $match = new RouteMatch($route);

        $this->assertSame($route, $match->getRoute());

        $match->setParams(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $match->getParams());
    }
}
