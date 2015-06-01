<?php

namespace Tonis\Router;

use Tonis\Router\TestAsset\NewRequestTrait;

/**
 * @coversDefaultClass Tonis\Router\Route
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    use NewRequestTrait;

    /**
     * @covers ::__construct
     * @covers ::getPath()
     */
    public function testGetPath()
    {
        $path = '/foo';
        $route = new Route($path, 'handler');
        $this->assertSame($path, $route->getPath());
    }

    /**
     * @covers ::getHandler
     */
    public function testGetHandler()
    {
        $callable = function () {
        };

        $route = new Route('/foo', $callable);
        $this->assertSame($callable, $route->getHandler());
    }

    /**
     * @covers ::getDefaults
     * @covers ::defaults
     */
    public function testDefaults()
    {
        $defaults = ['controller' => 'foo'];

        $route = new Route('/foo', 'handler');
        $this->assertEmpty($route->getDefaults());
        $route->defaults($defaults);
        $this->assertSame($defaults, $route->getDefaults());
    }

    /**
     * @covers ::getSecure
     * @covers ::secure
     */
    public function testSecure()
    {
        $route = new Route('/foo', 'handler');
        $this->assertNull($route->getSecure());
        $route->secure(true);
        $this->assertTrue($route->getSecure());
    }

    /**
     * @covers ::getAccepts
     * @covers ::accepts
     */
    public function testAccepts()
    {
        $route = new Route('/foo', 'handler');
        $this->assertSame(['*'], $route->getAccepts());
        $route->accepts(['application/json']);
        $this->assertSame(['application/json'], $route->getAccepts());
    }

    /**
     * @covers ::getMethods
     * @covers ::methods
     */
    public function testMethods()
    {
        $route = new Route('/foo', 'handler');
        $this->assertEmpty($route->getMethods());
        $route->methods(['GET', 'POST']);
        $this->assertSame(['GET', 'POST'], $route->getMethods());
    }

    /**
     * @covers ::init
     * @covers ::getRegex
     * @covers ::getTokens
     */
    public function testRegex()
    {
        $route = new Route('/foo/{id}/{name}/{date}', 'handler');
        $regex = $route->getRegex();
        $tokens = $route->getTokens();

        $expected = new \SplFixedArray(3);
        $expected[0] = ['id', false];
        $expected[1] = ['name', false];
        $expected[2] = ['date', false];

        $this->assertSame('/foo/(?<id>.*)/(?<name>.*)/(?<date>.*)', $regex);
        $this->assertInstanceOf('SplFixedArray', $tokens);
        $this->assertEquals($expected, $tokens);
    }

    /**
     * @covers ::init
     * @covers ::getRegex
     * @covers ::getTokens
     */
    public function testRegexWithConstraints()
    {
        $route = new Route('/foo/{id:\d+}', 'handler');
        $regex = $route->getRegex();
        $tokens = $route->getTokens();

        $expected = new \SplFixedArray(1);
        $expected[0] = ['id', false];

        $this->assertSame('/foo/(?<id>\d+)', $regex);
        $this->assertInstanceOf('SplFixedArray', $tokens);
        $this->assertEquals($expected, $tokens);
    }

    /**
     * @covers ::init
     * @covers ::getRegex
     * @covers ::getTokens
     */
    public function testRegexWithOptionalTokens()
    {
        $route = new Route('/foo/{id:\d+}{-slug?}', 'handler');

        $regex = $route->getRegex();
        $tokens = $route->getTokens();

        $expected = new \SplFixedArray(2);
        $expected[0] = ['id', false];
        $expected[1] = ['slug', true];

        $this->assertSame('/foo/(?<id>\d+)(?:-(?<slug>.*))?', $regex);
        $this->assertInstanceOf('SplFixedArray', $tokens);
        $this->assertEquals($expected, $tokens);
    }
}
