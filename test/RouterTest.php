<?php
namespace Tonis\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tonis\Router\TestAsset\NewRequestTrait;
use Zend\Diactoros\Response;

/**
 * @coversDefaultClass \Tonis\Router\Router
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    use NewRequestTrait;

    /** @var Router */
    private $router;

    /**
     * @covers ::__invoke
     */
    public function testMiddelwareInjectsAttributes()
    {
        $response = new Response();

        $this->router->add('/foo', 'foo.handler');

        $result = $this->router->__invoke(
            $this->newRequest('/foo'),
            $response,
            function(ServerRequestInterface $newRequest) use (&$ran) {
                $this->assertArrayHasKey('route.handler', $newRequest->getAttributes());
                return true;
            }
        );

        $this->assertTrue($result);

        $result = $this->router->__invoke($this->newRequest('/'), $response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    /**
     * @covers ::getLastMatch
     * @covers ::__construct
     */
    public function testGetLastMatch()
    {
        $router = new Router;
        $router->add('/foo', 'handler');

        $route = $router->match($this->newRequest('/foo'));

        $this->assertSame($route, $router->getLastMatch());
    }

    /**
     * @covers ::add
     */
    public function testAddProxiesToRouteCollection()
    {
        $this->router->add('foo', 'foo', 'foo');
        $this->assertTrue(isset($this->router->getRouteCollection()['foo']));
    }

    /**
     * @covers ::get
     * @covers ::post
     * @covers ::patch
     * @covers ::delete
     * @covers ::put
     * @covers ::addWithMethod
     * @covers ::getRouteCollection
     * @dataProvider httpMethodProvider
     *
     * @param string $method
     */
    public function testHttpMethods($method)
    {
        $router = new Router;
        $router->{$method}('/foo', 'handler');

        $router2 = new Router();
        $router2->add('/foo', 'handler', null)->methods([$method]);

        $this->assertEquals($router->getRouteCollection(), $router2->getRouteCollection());
    }

    /**
     * @covers ::matchRoute
     * @covers ::match
     */
    public function testMatchingRoutes()
    {
        $router = new Router;
        $router->add('/foo', 'handler');
        $router->add('/bar', 'handler');

        $this->assertInstanceOf(RouteMatch::class, $router->match($this->newRequest('/bar')));
        $this->assertInstanceOf(RouteMatch::class, $router->match($this->newRequest('/foo')));
        $this->assertNull($router->match($this->newRequest('/does/not/exist')));
    }

    /**
     * @covers ::assemble
     * @covers \Tonis\Router\Exception\RouteDoesNotExistException::__construct
     * @expectedException \Tonis\Router\Exception\RouteDoesNotExistException
     * @expectedExceptionMessage The route with name "foo" does not exist
     */
    public function testAssembleThrowsExceptionOnInvalidRouteName()
    {
        $router = $this->router;
        $router->assemble('foo');
    }

    /**
     * @covers ::assemble
     */
    public function testAssemble()
    {
        $router = $this->router;
        $router->add('/foo', 'handler', 'foo');
        $this->assertSame('/foo', $router->assemble('foo'));
    }

    /**
     * @covers ::assemble
     * @covers \Tonis\Router\Exception\RouteDoesNotExistException::__construct
     * @expectedException \Tonis\Router\Exception\RouteDoesNotExistException
     * @expectedExceptionMessage The route with name "foo" does not exist
     */
    public function testAssembleThrowsExceptionForMissingRoute()
    {
        $router = $this->router;
        $router->assemble('foo');
    }

    /**
     * @covers ::assemble
     */
    public function testAssembleWithParams()
    {
        $router = $this->router;
        $router->add('/foo/{bar}/{baz}', 'handler', 'foo');
        $this->assertSame('/foo/1/2', $router->assemble('foo', ['bar' => 1, 'baz' => 2]));
    }

    /**
     * @covers ::assemble
     * @covers \Tonis\Router\Exception\MissingParameterException::__construct
     * @expectedException \Tonis\Router\Exception\MissingParameterException
     * @expectedExceptionMessage Cannot assemble route "/foo/{bar}": missing required parameter "bar"
     */
    public function testAssembleWithParamsThrowsExceptionIfMissingParam()
    {
        $router = $this->router;
        $router->add('/foo/{bar}', 'handler', 'foo');
        $router->assemble('foo');
    }

    /**
     * @covers ::assemble
     */
    public function testAssembleWithOptionalParams()
    {
        $router = $this->router;
        $router->add('/foo{/bar?}', 'handler', 'foo');
        $this->assertSame('/foo', $router->assemble('foo'));
        $this->assertSame('/foo/baz', $router->assemble('foo', ['bar' => 'baz']));
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
     * @param RouteCollection $router
     * @return Route[]
     */
    protected function getRoutesFromCollection(RouteCollection $router)
    {
        $refl = new \ReflectionClass($router);
        $router = $refl->getProperty('routes');
        $router->setAccessible(true);

        return $router->getValue($router);
    }

    protected function setUp()
    {
        $this->router = new Router;
    }
}
