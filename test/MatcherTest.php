<?php
namespace Tonis\Router;

class MatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var Matcher */
    private $matcher;

//    /**
//     * @covers ::init
//     * @covers ::match
//     */
//    public function testNoMatch()
//    {
//        $route = new Route('foo', '/bar');
//        $this->assertNull($route->match($this->newRequest('/foo')));
//    }
//
//    /**
//     * @covers ::init
//     * @covers ::match
//     */
//    public function testNoMatchForRegexEndOfLine()
//    {
//        $route = new Route('foo', '/foobar');
//        $this->assertNull($route->match($this->newRequest('/foo')));
//    }
//
//    /**
//     * @covers ::init
//     * @covers ::match
//     */
//    public function testMatchWithMethods()
//    {
//        $r = new Route('/foobar', 'handler');
//        $r->withMethods(['get']);
//
//        $this->assertNull($r->match($this->newRequest('/foobar', ['REQUEST_METHOD' => 'POST'])));
//        $this->assertInstanceOf(
//            'Tonis\Router\Match',
//            $r->match($this->newRequest('/foobar', ['REQUEST_METHOD' => 'GET']))
//        );
//    }
//
//    /**
//     * @covers ::init
//     * @covers ::match
//     */
//    public function testMatchReturnsMatch()
//    {
//        $route = new Route('/foo/{id}', 'handler');
//        $match = $route->match($this->newRequest('/foo/bar'));
//
//        $this->assertInstanceOf('Tonis\Router\Match', $match);
//        $this->assertSame($route, $match->getRoute());
//        $this->assertSame('bar', $match->getParam('id'));
//    }
//
//    /**
//     * @covers ::init
//     * @covers ::match
//     */
//    public function testMatchWithConstraints()
//    {
//        $route = new Route('/foo/{id:\d+}', 'handler');
//        $this->assertNull($route->match($this->newRequest('/foo/bar')));
//
//        $match = $route->match($this->newRequest('/foo/1234'));
//        $this->assertInstanceOf('Tonis\Router\Match', $match);
//        $this->assertSame($route, $match->getRoute());
//        $this->assertSame('1234', $match->getParam('id'));
//    }
//
//    /**
//     * @covers ::init
//     * @covers ::match
//     */
//    public function testMatchWithOptionalTokens()
//    {
//        $route = new Route('/foo/{id:\d+}{-slug?}', 'handler');
//
//        $match = $route->match($this->newRequest('/foo/1'));
//        $this->assertInstanceOf('Tonis\Router\Match', $match);
//        $this->assertSame('1', $match->getParam('id'));
//        $this->assertEmpty($match->getParam('slug'));
//
//        $match = $route->match($this->newRequest('/foo/1-testing'));
//        $this->assertInstanceOf('Tonis\Router\Match', $match);
//        $this->assertSame('1', $match->getParam('id'));
//        $this->assertSame('testing', $match->getParam('slug'));
//    }
//
//    /**
//     * @covers ::assemble
//     * @covers ::init
//     * @covers \Tonis\Router\Exception\MissingParameterException::__construct
//     * @expectedException \Tonis\Router\Exception\MissingParameterException
//     * @expectedExceptionMessage Cannot assemble route "/foo/{id}": missing required parameter "id"
//     */
//    public function testAssembleThrowsExceptionOnMissingRequiredParameter()
//    {
//        $route = new Route('/foo/{id}', 'handler');
//        $route->assemble();
//    }
//
//    /**
//     * @covers ::assemble
//     */
//    public function testAssembleWithNoTokens()
//    {
//        $route = new Route('/foo', 'handler');
//        $this->assertSame('/foo', $route->assemble());
//    }
//
//    /**
//     * @covers ::assemble
//     */
//    public function testAssemble()
//    {
//        $route = new Route('/foo/{name}', 'handler');
//        $this->assertSame('/foo/bar', $route->assemble(['name' => 'bar']));
//    }
//
//    /**
//     * @covers ::assemble
//     * @covers ::init
//     */
//    public function testAssembleWithOptionalParams()
//    {
//        $route = new Route('/foo/{id?}', 'handler');
//        $this->assertSame('/foo/', $route->assemble());
//
//        $route = new Route('/foo{/id?}', 'handler');
//        $this->assertSame('/foo', $route->assemble());
//
//        $route = new Route('/foo/{id:\d+}{-slug?}', 'handler');
//        $this->assertSame('/foo/1', $route->assemble(['id' => 1]));
//        $this->assertSame('/foo/1-test', $route->assemble(['id' => 1, 'slug' => 'test']));
//    }
//
//    /**
//     * @cowers ::match
//     * @covers ::init
//     * @covers ::withDefaults
//     */
//    public function testMatchWithDefaults()
//    {
//        $route = new Route('/foo', 'handler');
//        $route->withDefaults(['controller' => 'foo']);
//
//        $match = $route->match($this->newRequest('/foo'));
//        $this->assertSame('foo', $match->getParam('controller'));
//    }

    protected function setUp()
    {

    }
}
