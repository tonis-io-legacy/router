<?php
namespace Tonis\Router\Rule;

use Tonis\Router\Match;
use Tonis\Router\Route;
use Tonis\Router\TestAsset\NewRequestTrait;

/**
 * @coversDefaultClass \Tonis\Router\Rule\Path
 */
class PathTest extends \PHPUnit_Framework_TestCase
{
    use NewRequestTrait;

    /** @var Path */
    private $rule;

    /**
     * @covers ::__invoke
     */
    public function testSimple()
    {
        $route = new Route('/foo/bar', 'handler');
        $match = new Match($route);

        $this->assertTrue($this->rule->__invoke($this->newRequest('/foo/bar'), $match));
        $this->assertEmpty($match->getParams());

        $this->assertFalse($this->rule->__invoke($this->newRequest('/foo'), $match));
        $this->assertEmpty($match->getParams());
    }

    /**
     * @covers ::__invoke
     */
    public function testParamMatch()
    {
        $route = new Route('/foo/{name}', 'handler');
        $match = new Match($route);

        $this->assertTrue($this->rule->__invoke($this->newRequest('/foo/bar'), $match));
        $this->assertCount(1, $match->getParams());
        $this->assertSame('bar', $match->getParams()['name']);
    }

    protected function setUp()
    {
        $this->rule = new Path();
    }
}
