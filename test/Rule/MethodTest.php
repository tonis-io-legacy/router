<?php
namespace Tonis\Router\Rule;

use Tonis\Router\Route;
use Tonis\Router\RouteMatch;
use Tonis\Router\TestAsset\NewRequestTrait;

/**
 * @coversDefaultClass \Tonis\Router\Rule\Method
 */
class MethodTest extends \PHPUnit_Framework_TestCase
{
    use NewRequestTrait;

    /** @var Method */
    private $rule;

    /**
     * @covers ::__invoke
     */
    public function testSimple()
    {
        $route = new Route('/foo', 'handler');
        $match = new RouteMatch($route);

        $this->assertTrue($this->rule->__invoke($this->newRequest('/foo'), $match));

        $route->methods(['POST']);
        $this->assertFalse($this->rule->__invoke($this->newRequest('/foo'), $match));
        $this->assertTrue($this->rule->__invoke($this->newRequest('/foo', ['REQUEST_METHOD' => 'POST']), $match));
        $this->assertTrue($this->rule->__invoke($this->newRequest('/foo', ['REQUEST_METHOD' => 'pOsT']), $match));
    }

    protected function setUp()
    {
        $this->rule = new Method();
    }
}
