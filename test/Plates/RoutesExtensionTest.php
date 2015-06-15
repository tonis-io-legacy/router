<?php
namespace Tonis\Router\Plates;

use League\Plates\Engine;
use Tonis\Router\Router;

/**
 * @coversDefaultClass \Tonis\Router\Plates\RouteExtension
 */
class RouteExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var RouteExtension */
    private $ext;
    /** @var Router */
    private $router;

    /**
     * @covers ::__construct
     * @covers ::register
     */
    public function testRegister()
    {
        $engine = new Engine;
        $this->ext->register($engine);

        $this->assertNotNull($engine->getFunction('url'));
    }

    /**
     * @covers ::urlFunction
     */
    public function testUrlFunction()
    {
        $this->assertSame(
            $this->router->assemble('foo', ['id' => 1234]),
            $this->ext->urlFunction('foo', ['id' => 1234])
        );
    }

    protected function setUp()
    {
        $this->router = new Router;
        $this->router->add('/foo', 'foo', 'foo');

        $this->ext = new RouteExtension($this->router);
    }
}
