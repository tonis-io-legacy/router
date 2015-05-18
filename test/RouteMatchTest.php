<?php

namespace Tonis\Router;

/**
 * @coversDefaultClass Tonis\Router\RouteMatch
 */
class RouterMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getRoute
     */
    public function testGetRoute()
    {
        $route = new Route('foo', '/foo');
        $match = new RouteMatch($route);

        $this->assertSame($route, $match->getRoute());
    }

    /**
     * @covers ::set
     */
    public function testsetParam()
    {
        $route = new Route('foo', '/foo');
        $match = new RouteMatch($route);
        $match->setParam('foo', 'bar');
        $this->assertSame('bar', $match->getParam('foo'));
    }

    /**
     * @covers ::get
     */
    public function testgetParam()
    {
        $route = new Route('foo', '/foo');
        $match = new RouteMatch($route, ['id' => 1]);

        $this->assertSame(1, $match->getParam('id'));
        $this->assertNull($match->getParam('doesnotexist'));
    }

    /**
     * @covers ::get
     */
    public function testGetWithDefault()
    {
        $route = new Route('foo', '/foo');
        $match = new RouteMatch($route);
        $this->assertSame(1, $match->getParam('id', 1));
    }

    /**
     * @covers ::getParams, ::setParams
     */
    public function testSetGetParams()
    {
        $params = ['id' => 1, 'foo' => 'bar'];
        $route = new Route('foo', '/foo');
        $match = new RouteMatch($route, $params);

        $this->assertSame($params, $match->getParams());

        $params2 = ['id' => 2, 'foo' => 'baz'];
        $match->setParams($params2);
        $this->assertSame($params2, $match->getParams());
    }
}
