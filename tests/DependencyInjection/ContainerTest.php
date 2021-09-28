<?php


namespace Tests\DependencyInjection;

use App\DependencyInjection\Container;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use Tests\ConsoleDebugger;
use Tests\DependencyInjection\Classes\Foo;

class ContainerTest extends TestCase
{
    use ConsoleDebugger;

    /**
     * @var Container $container
     */
    private $container;

    protected function setUp(): void
    {
       $this->container = new Container();
    }

    public function testSingleton(): void
    {
        $this->assertEquals(
            spl_object_id($this->container->get(Foo::class)),
            spl_object_id($this->container->get(Foo::class))
        );

    }

    /**
     * @dataProvider providerGetInstanceException
     * @param string $id
     */
    public function testGetInstanceException(string $id)
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $this->container->get($id);
    }

    public function providerGetInstanceException()
    {
        yield [
            "Tests\DependencyInjection\Classes\Unknown"
        ];

        yield [
            "hello world"
        ];
    }
}