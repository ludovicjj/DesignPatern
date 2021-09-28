<?php


namespace Tests\DependencyInjection;

use App\DependencyInjection\Container;
use App\DependencyInjection\Definition;
use PHPUnit\Framework\TestCase;
use Tests\ConsoleDebugger;
use Tests\DependencyInjection\Classes\Foo;
use Tests\DependencyInjection\Classes\Interfaces\FooInterface;

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

    public function testResolveClassWithoutDependency()
    {
        $foo = $this->container->get(Foo::class);
        $definition = $this->container->getDefinition(Foo::class);

        $this->assertInstanceOf(Foo::class, $foo);
        $this->assertInstanceOf(Definition::class, $definition);
        $this->assertCount(0, $definition->getAlias());
        $this->assertEquals(Foo::class, $definition->getId());
    }

    public function testResolveClassWithoutDependencyAndWithAlias()
    {
        $this->container->addAlias("FooInterface", Foo::class);
        $this->container->get(Foo::class);
        $definition = $this->container->getDefinition(Foo::class);

        $this->assertCount(1, $definition->getAlias());
    }

    public function testResolveInterface()
    {
        $this->container->addAlias(FooInterface::class, Foo::class);
        $foo = $this->container->get(FooInterface::class);
        $this->assertInstanceOf(Foo::class, $foo);
    }
}