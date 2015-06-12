<?php
namespace Tonis\Router;

use Tonis\Router\TestAsset\NewRequestTrait;

/**
 * @coversDefaultClass \Tonis\Router\RouteCollection
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    use NewRequestTrait;

    /** @var RouteCollection */
    private $routeCollection;

    /**
     * @covers ::offsetExists
     * @covers ::offsetGet
     * @covers ::offsetSet
     * @covers ::offsetUnset
     */
    public function testArrayAccess()
    {
        $route1 = new Route('foo', 'foo');
        $route2 = new Route('foo', 'foo');

        $this->assertFalse(isset($this->routeCollection['foo']));
        $this->routeCollection['foo'] = $route1;
        $this->assertTrue(isset($this->routeCollection['foo']));
        $this->routeCollection[] = $route2;
        $this->assertTrue(isset($this->routeCollection[0]));

        $this->assertSame($route1, $this->routeCollection['foo']);
        $this->assertSame($route2, $this->routeCollection[0]);

        unset($this->routeCollection['foo']);
        $this->assertFalse(isset($this->routeCollection['foo']));
    }

    /**
     * @covers ::current
     * @covers ::next
     * @covers ::key
     * @covers ::valid
     * @covers ::rewind
     */
    public function testTraversable()
    {
        $route1 = new Route('foo', 'foo');
        $route2 = new Route('bar', 'bar');

        $this->routeCollection[] = $route1;
        $this->routeCollection[] = $route2;

        foreach ($this->routeCollection as $key => $route) {
            if ($key == 0) {
                $this->assertSame($route1, $route);
            } else {
                $this->assertSame($route2, $route);
            }
        }
    }

    /**
     * @covers ::offsetGet
     * @expectedException \Tonis\Router\Exception\RouteDoesNotExistException
     * @expectedExceptionMessage The route with name "foo" does not exist
     */
    public function testOffsetGetThrowsExceptionForMissing()
    {
        $this->routeCollection['foo'];
    }

    /**
     * @covers ::offsetSet
     * @expectedException \Tonis\Router\Exception\RouteExistsException
     * @expectedExceptionMessage The route with name "foo" already exists
     */
    public function testOffsetSetThrowsExceptionForDuplicate()
    {
        $this->routeCollection['foo'] = new Route('foo', 'foo');
        $this->routeCollection['foo'] = new Route('foo', 'foo');
    }

    /**
     * @covers ::offsetSet
     * @covers \Tonis\Router\Exception\InvalidRouteException::__construct
     * @expectedException \Tonis\Router\Exception\InvalidRouteException
     * @expectedExceptionMessage Route must be an instance of Tonis\Router\Route
     */
    public function testOffsetSetThrowsExceptionForInvalidType()
    {
        $this->routeCollection['foo'] = true;
    }

    /**
     * @covers ::offsetUnset
     * @expectedException \Tonis\Router\Exception\RouteDoesNotExistException
     * @expectedExceptionMessage The route with name "foo" does not exist
     */
    public function testOffsetUnsetThrowsExceptionForMissingRoute()
    {
        unset($this->routeCollection['foo']);
    }

    /**
     * @covers ::count
     */
    public function testCountable()
    {
        $this->assertCount(0, $this->routeCollection);
        $this->routeCollection->add('foo', 'foo');
        $this->assertCount(1, $this->routeCollection);
    }

    /**
     * @covers ::add
     */
    public function testAddingUnnamedRoutes()
    {
        $collection = $this->routeCollection;
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
        $collection = $this->routeCollection;
        $collection->add('/foo', 'handler', 'foo');
        $collection->add('/bar', 'handler', 'bar');

        $namedRoutes = $this->getRoutesFromCollection($collection);

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
        $collection = $this->routeCollection;
        $collection->add('/foo', 'handler', 'foo');
        $collection->add('/foo2', 'handler', 'foo');
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
    
    protected function setUp()
    {
        $this->routeCollection = new RouteCollection;
    }
}
