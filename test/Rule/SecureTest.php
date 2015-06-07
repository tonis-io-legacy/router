<?php
namespace Tonis\Router\Rule;

use Tonis\Router\Route;
use Tonis\Router\RouteMatch;
use Tonis\Router\TestAsset\NewRequestTrait;

/**
 * @coversDefaultClass \Tonis\Router\Rule\Secure
 */
class SecureTest extends \PHPUnit_Framework_TestCase
{
    use NewRequestTrait;

    /** @var Secure */
    private $rule;

    /**
     * @covers ::__invoke
     */
    public function testSimple()
    {
        $route = new Route('/foo', 'handler');
        $match = new RouteMatch($route);

        $this->assertTrue($this->rule->__invoke($this->newRequest('/foo'), $match));

        $route->secure(true);
        $this->assertFalse($this->rule->__invoke($this->newRequest('/foo', ['HTTPS' => 'off']), $match));
        $this->assertTrue($this->rule->__invoke($this->newRequest('/foo', ['HTTPS' => 'on']), $match));
        $this->assertTrue(
            $this->rule->__invoke(
                $this->newRequest('/foo', ['SERVER_PORT' => 443, 'SERVER_NAME' => 'foo.com']),
                $match
            )
        );

        $route->secure(false);
        $this->assertTrue($this->rule->__invoke($this->newRequest('/foo', ['HTTPS' => 'off']), $match));
        $this->assertFalse($this->rule->__invoke($this->newRequest('/foo', ['HTTPS' => 'on']), $match));
        $this->assertFalse(
            $this->rule->__invoke(
                $this->newRequest('/foo', ['SERVER_PORT' => 443, 'SERVER_NAME' => 'foo.com']),
                $match
            )
        );
    }

    protected function setUp()
    {
        $this->rule = new Secure();
    }
}
