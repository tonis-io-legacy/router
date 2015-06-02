<?php
namespace Tonis\Router;

use Tonis\Router\TestAsset\NewRequestTrait;

/**
 * @coversDefaultClass \Tonis\Router\Collection
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    use NewRequestTrait;

    /**
     * @covers ::__construct
     * @covers ::add
     */
    public function testAddingUnnamedRoutes()
    {
        $collection = new Collection();
        $collection->add('/foo', 'handler');
        $collection->add('/bar', 'handler');

        $routes = $this->getRoutesFromCollection($collection);

        $this->assertInternalType('array', $routes);
        $this->assertCount(2, $routes);
    }

    /**
     * @covers ::getLastMatch
     */
    public function testGetLastMatch()
    {
        $collection = new Collection();
        $collection->add('/foo', 'handler');

        $route = $collection->match($this->newRequest('/foo'));

        $this->assertSame($route, $collection->getLastMatch());
    }

    /**
     * @covers ::get
     * @covers ::post
     * @covers ::patch
     * @covers ::delete
     * @covers ::put
     * @covers ::addWithMethod
     * @covers ::getRoutes
     * @dataProvider httpMethodProvider
     *
     * @param string $method
     */
    public function testHttpMethods($method)
    {
        $collection = new Collection();
        $collection->{$method}('/foo', 'handler');

        $collection2 = new Collection();
        $collection2->add('/foo', 'handler', null)->methods([$method]);

        $this->assertEquals($collection->getRoutes(), $collection2->getRoutes());
    }

    /**
     * @covers ::add
     */
    public function testAddingNamedRoutes()
    {
        $collection = new Collection();
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
        $collection = new Collection();
        $collection->add('/foo', 'handler', 'foo');
        $collection->add('/foo2', 'handler', 'foo');
    }

    /**
     * @covers ::matchRoute
     * @covers ::match
     */
    public function testMatchingRoutes()
    {
        $collection = new Collection();
        $collection->add('/foo', 'handler');
        $collection->add('/bar', 'handler');

        $this->assertInstanceOf('Tonis\Router\Match', $collection->match($this->newRequest('/bar')));
        $this->assertInstanceOf('Tonis\Router\Match', $collection->match($this->newRequest('/foo')));
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
        $routes = new Collection();
        $routes->assemble('foo');
    }

    /**
     * @covers ::assemble
     */
    public function testAssemble()
    {
        $routes = new Collection();
        $routes->add('/foo', 'handler', 'foo');
        $this->assertSame('/foo', $routes->assemble('foo'));
    }

    /**
     * @covers ::assemble
     * @covers \Tonis\Router\Exception\RouteDoesNotExistException::__construct
     * @expectedException \Tonis\Router\Exception\RouteDoesNotExistException
     * @expectedExceptionMessage The route with name "foo" does not exist
     */
    public function testAssembleThrowsExceptionForMissingRoute()
    {
        $routes = new Collection();
        $routes->assemble('foo');
    }

    /**
     * @covers ::assemble
     */
    public function testAssembleWithParams()
    {
        $routes = new Collection();
        $routes->add('/foo/{bar}/{baz}', 'handler', 'foo');
        $this->assertSame('/foo/1/2', $routes->assemble('foo', ['bar' => 1, 'baz' => 2]));
    }

    /**
     * @covers ::assemble
     * @covers \Tonis\Router\Exception\MissingParameterException::__construct
     * @expectedException \Tonis\Router\Exception\MissingParameterException
     * @expectedExceptionMessage Cannot assemble route "/foo/{bar}": missing required parameter "bar"
     */
    public function testAssembleWithParamsThrowsExceptionIfMissingParam()
    {
        $routes = new Collection();
        $routes->add('/foo/{bar}', 'handler', 'foo');
        $routes->assemble('foo');
    }

    /**
     * @covers ::assemble
     */
    public function testAssembleWithOptionalParams()
    {
        $routes = new Collection();
        $routes->add('/foo{/bar?}', 'handler', 'foo');
        $this->assertSame('/foo', $routes->assemble('foo'));
        $this->assertSame('/foo/baz', $routes->assemble('foo', ['bar' => 'baz']));
    }

    /**
     * @covers ::assemble
     */
    public function httpMethodProvider()
    {
        return [
            ['GET'],
            ['POST'],
            ['PUT'],
            ['DELETE'],
            ['PATCH'],
        ];
    }

    /**
     * @param Collection $collection
     * @return Route[]
     */
    protected function getRoutesFromCollection(Collection $collection)
    {
        $refl = new \ReflectionClass($collection);
        $routes = $refl->getProperty('routes');
        $routes->setAccessible(true);

        return $routes->getValue($collection);
    }
}
