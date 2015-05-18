<?php

namespace Tonis\Router;
use Phly\Http\ServerRequestFactory;

/**
 * @coversDefaultClass Tonis\Router\Route
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getName()
     */
    public function testGetName()
    {
        $name = 'foo';
        $route = new Route($name, '/foo');
        $this->assertSame($name, $route->getName());
    }

    /**
     * @covers ::getPath()
     */
    public function testGetPath()
    {
        $path = '/foo';
        $route = new Route('foo', $path);
        $this->assertSame($path, $route->getPath());
    }

    /**
     * @covers ::init
     * @covers ::match
     */
    public function testNoMatch()
    {
        $route = new Route('foo', '/bar');
        $this->assertNull($route->match($this->newRequest('/foo')));
    }

    /**
     * @covers ::init
     * @covers ::match
     */
    public function testNoMatchForRegexEndOfLine()
    {
        $route = new Route('foo', '/foobar');
        $this->assertNull($route->match($this->newRequest('/foo')));
    }

    /**
     * @covers ::init
     * @covers ::match
     */
    public function testMatchWithMethods()
    {
        $r = new Route('foo', '/foobar');
        $r->methods(['get']);

        $this->assertNull($r->match($this->newRequest('/foobar', ['REQUEST_METHOD' => 'POST'])));
        $this->assertInstanceOf(
            'Tonis\Router\RouteMatch',
            $r->match($this->newRequest('/foobar', ['REQUEST_METHOD' => 'GET']))
        );
    }

    /**
     * @covers ::init
     * @covers ::match
     */
    public function testMatchReturnsRouteMatch()
    {
        $route = new Route('foo', '/foo/{id}');
        $match = $route->match($this->newRequest('/foo/bar'));

        $this->assertInstanceOf('Tonis\Router\RouteMatch', $match);
        $this->assertSame($route, $match->getRoute());
        $this->assertSame('bar', $match->getParam('id'));
    }

    /**
     * @covers ::init
     * @covers ::match
     */
    public function testMatchWithConstraints()
    {
        $route = new Route('foo', '/foo/{id:\d+}');
        $this->assertNull($route->match($this->newRequest('/foo/bar')));

        $match = $route->match($this->newRequest('/foo/1234'));
        $this->assertInstanceOf('Tonis\Router\RouteMatch', $match);
        $this->assertSame($route, $match->getRoute());
        $this->assertSame('1234', $match->getParam('id'));
    }

    /**
     * @covers ::init
     * @covers ::match
     */
    public function testMatchWithOptionalTokens()
    {
        $route = new Route('foo', '/foo/{id:\d+}{-slug?}');

        $match = $route->match($this->newRequest('/foo/1'));
        $this->assertInstanceOf('Tonis\Router\RouteMatch', $match);
        $this->assertSame('1', $match->getParam('id'));
        $this->assertEmpty($match->getParam('slug'));

        $match = $route->match($this->newRequest('/foo/1-testing'));
        $this->assertInstanceOf('Tonis\Router\RouteMatch', $match);
        $this->assertSame('1', $match->getParam('id'));
        $this->assertSame('testing', $match->getParam('slug'));
    }

    /**
     * @covers ::assemble
     * @covers ::init
     * @covers \Tonis\Router\Exception\MissingParameterException::__construct
     * @expectedException \Tonis\Router\Exception\MissingParameterException
     * @expectedExceptionMessage Cannot assemble route "foo": missing required parameter "id"
     */
    public function testAssembleThrowsExceptionOnMissingRequiredParameter()
    {
        $route = new Route('foo', '/foo/{id}');
        $route->assemble();
    }

    /**
     * @covers ::assemble
     */
    public function testAssembleWithNoTokens()
    {
        $route = new Route('foo', '/foo');
        $this->assertSame('/foo', $route->assemble());
    }

    /**
     * @covers ::assemble
     */
    public function testAssemble()
    {
        $route = new Route('foo', '/foo/{name}');
        $this->assertSame('/foo/bar', $route->assemble(['name' => 'bar']));
    }

    /**
     * @covers ::assemble
     * @covers ::init
     */
    public function testAssembleWithOptionalParams()
    {
        $route = new Route('foo', '/foo/{id?}');
        $this->assertSame('/foo/', $route->assemble());

        $route = new Route('foo', '/foo{/id?}');
        $this->assertSame('/foo', $route->assemble());

        $route = new Route('foo', '/foo/{id:\d+}{-slug?}');
        $this->assertSame('/foo/1', $route->assemble(['id' => 1]));
        $this->assertSame('/foo/1-test', $route->assemble(['id' => 1, 'slug' => 'test']));
    }

    /**
     * @cowers ::match
     * @covers ::init
     * @covers ::setDefaults
     */
    public function testMatchWithDefaults()
    {
        $route = new Route('foo', '/foo');
        $route->defaults(['controller' => 'foo']);

        $match = $route->match($this->newRequest('/foo'));
        $this->assertSame('foo', $match->getParam('controller'));
    }

    /**
     * @covers ::defaults
     */
    public function testRouteDefaults()
    {
        $defaults = ['controller' => 'foo'];

        $route = new Route('foo', '/foo');
        $route->defaults($defaults);

        $refl = new \ReflectionClass($route);
        $defs = $refl->getProperty('defaults');
        $defs->setAccessible(true);

        $this->assertSame($defaults, $defs->getValue($route));
    }

    /**
     * @param string $path
     * @param array $server
     * @return \Phly\Http\ServerRequest
     */
    protected function newRequest($path, array $server = [])
    {
        $server['REQUEST_URI'] = $path;
        $server = array_merge($_SERVER, $server);
        return ServerRequestFactory::fromGlobals($server);
    }
}
