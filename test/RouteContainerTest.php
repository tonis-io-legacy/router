<?php
namespace Tonis\Router;
use Phly\Http\ServerRequestFactory;

/**
 * @coversDefaultClass \Tonis\Router\RouteCollection
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::add, ::getRoutes
     */
    public function testAddingRoutes()
    {
        $router = new RouteCollection();
        $router->add('foo', '/foo');
        $router->add('bar', '/bar');
        $routes = $router->getRoutes();
        $this->assertInstanceOf('ArrayObject', $routes);
        $this->assertArrayHasKey('foo', $routes);
        $this->assertArrayHasKey('bar', $routes);
    }
    /**
     * @covers ::add, \Tonis\Router\Exception\RouteExistsException::__construct
     * @expectedException \Tonis\Router\Exception\RouteExistsException
     * @expectedExceptionMessage The route with name "foo" already exists
     */
    public function testAddingSameRouteNameThrowsException()
    {
        $router = new RouteCollection();
        $router->add('foo', '/foo');
        $router->add('foo', '/foo2');
    }
    /**
     * @covers ::add
     */
    public function testAddingRoutesWithNoName()
    {
        $router = new RouteCollection();
        $router->add(null, '/foo');
        $router->add(null, '/foo/bar');
        $router->add('foo', '/foo');
        $routes = $router->getRoutes();
        $this->assertCount(3, $routes);
        $this->assertArrayHasKey(0, $routes);
        $this->assertArrayHasKey(1, $routes);
        $this->assertArrayHasKey('foo', $routes);
    }
    /**
     * @covers ::match
     */
    public function testMatchingRoutes()
    {
        $router = new RouteCollection();
        $router->add('foo', '/foo');
        $router->add(null, '/bar');
        $this->assertInstanceOf('Tonis\Router\RouteMatch', $router->match($this->newRequest('/bar')));
        $this->assertInstanceOf('Tonis\Router\RouteMatch', $router->match($this->newRequest('/foo')));
        $this->assertNull($router->match($this->newRequest('/does/not/exist')));
    }
    /**
     * @covers ::assemble, \Tonis\Routre\Exception\RouteDoesNotExistException::__construct
     * @expectedException \Tonis\Router\Exception\RouteDoesNotExistException
     * @expectedExceptionMessage The route with name "foo" does not exist
     */
    public function testAssembleThrowsExceptionOnInvalidRouteName()
    {
        $router = new RouteCollection();
        $router->assemble('foo');
    }
    /**
     * @covers ::assemble
     */
    public function testAssemblingRoutes()
    {
        $router = new RouteCollection();
        $router->add('foo', '/foo');
        $this->assertSame('/foo', $router->assemble('foo'));
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
