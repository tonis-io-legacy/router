<?php
namespace Tonis\Router;

use Zend\Diactoros\ServerRequestFactory;

/**
 * @coversDefaultClass \Tonis\Router\RouteCollection
 */
class RouteCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::add
     */
    public function testAddingUnamedRoutes()
    {
        $collection = new RouteCollection();
        $collection->add('/foo', 'handler');
        $collection->add('/bar', 'handler');

        $routes = $this->getRoutesFromCollection($collection);

        $this->assertInternalType('array', $routes);
        $this->assertCount(2, $routes);
    }

    /**
     * @covers ::add
     */
    public function testAddingNamedRoutes()
    {
        $collection = new RouteCollection();
        $collection->add('/foo', 'handler', 'foo');
        $collection->add('/bar', 'handler', 'bar');

        $namedRoutes = $this->getNamedRoutesFromCollection($collection);

        $this->assertInternalType('array', $namedRoutes);
        $this->assertArrayHasKey('foo', $namedRoutes);
        $this->assertArrayHasKey('bar', $namedRoutes);
    }

    /**
     * @covers ::add
     * @covers \Tonis\Router\Exception\RouteExistsException::__construct
     * @expectedException \Tonis\Router\Exception\RouteExistsException
     * @expectedExceptionMessage The route with name "foo" already exists
     */
    public function testAddingSameRouteNameThrowsException()
    {
        $collection = new RouteCollection();
        $collection->add('/foo', 'handler', 'foo');
        $collection->add('/foo2', 'handler', 'foo');
    }

    /**
     * @covers ::match
     */
    public function testMatchingRoutes()
    {
        $collection = new RouteCollection();
        $collection->add('/foo', 'handler');
        $collection->add('/bar', 'handler');

        $this->assertInstanceOf('Tonis\Router\RouteMatch', $collection->match($this->newRequest('/bar')));
        $this->assertInstanceOf('Tonis\Router\RouteMatch', $collection->match($this->newRequest('/foo')));
        $this->assertNull($collection->match($this->newRequest('/does/not/exist')));
    }

    /**
     * @covers ::assemble
     * @covers \Tonis\Router\Exception\RouteDoesNotExistException::__construct
     * @expectedException \Tonis\Router\Exception\RouteDoesNotExistException
     * @expectedExceptionMessage The route with name "foo" does not exist
     */
    public function testAssembleThrowsExceptionOnInvalidRouteName()
    {
        $routes = new RouteCollection();
        $routes->assemble('foo');
    }

    /**
     * @covers ::assemble
     */
    public function testAssemblingRoutes()
    {
        $routes = new RouteCollection();
        $routes->add('/foo', 'handler', 'foo');
        $this->assertSame('/foo', $routes->assemble('foo'));
    }

    /**
     * @param string $path
     * @param array $server
     * @return \Zend\Diactoros\ServerRequest
     */
    protected function newRequest($path, array $server = [])
    {
        $server['REQUEST_URI'] = $path;
        $server = array_merge($_SERVER, $server);

        return ServerRequestFactory::fromGlobals($server);
    }

    /**
     * @param RouteCollection $collection
     * @return Route[]
     */
    protected function getRoutesFromCollection(RouteCollection $collection)
    {
        $refl = new \ReflectionClass($collection);
        $routes = $refl->getProperty('routes');
        $routes->setAccessible(true);

        return $routes->getValue($collection);
    }

    /**
     * @param RouteCollection $collection
     * @return Route[]
     */
    protected function getNamedRoutesFromCollection(RouteCollection $collection)
    {
        $refl = new \ReflectionClass($collection);
        $routes = $refl->getProperty('namedRoutes');
        $routes->setAccessible(true);

        return $routes->getValue($collection);
    }
}
