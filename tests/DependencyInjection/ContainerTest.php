<?php


namespace Tests\DependencyInjection;

use App\DependencyInjection\Container;
use App\DependencyInjection\Definition;
use PHPUnit\Framework\TestCase;
use Tests\ConsoleDebugger;
use Tests\DependencyInjection\Classes\Bar;
use Tests\DependencyInjection\Classes\Foo;
use Tests\DependencyInjection\Classes\Interfaces\FooInterface;
use Tests\DependencyInjection\Classes\User;
use DateTimeImmutable;

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

    public function testResolveClassWithRegisterParameters(): void
    {
        $this->container
            ->addParameter("lastname", "Doe")
            ->addParameter("firstname", "John")
            ->addParameter("birthday", new DateTimeImmutable("now"))
            ->addParameter("info", ["age" => 18]);

        $user = $this->container->get(User::class);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals("Doe", $user->getLastname());
        $this->assertEquals("John", $user->getFirstname());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getBirthday());
        $this->assertIsArray($user->getInfo());
        $this->assertArrayHasKey("age", $user->getInfo());
        $this->assertTrue(in_array(18, $user->getInfo()));
    }

    public function testResolveClassWithoutDependency(): void
    {
        $foo = $this->container->get(Foo::class);
        $definition = $this->container->getDefinition(Foo::class);

        $this->assertInstanceOf(Foo::class, $foo);
        $this->assertInstanceOf(Definition::class, $definition);
        $this->assertCount(0, $definition->getAlias());
        $this->assertEquals(Foo::class, $definition->getId());
    }

    public function testResolveClassWithoutDependencyAndWithAlias(): void
    {
        $this->container->addAlias("FooInterface", Foo::class);
        $this->container->get(Foo::class);
        $definition = $this->container->getDefinition(Foo::class);

        $this->assertCount(1, $definition->getAlias());
    }

    public function testResolveInterface(): void
    {
        $this->container->addAlias(FooInterface::class, Foo::class);
        $foo = $this->container->get(FooInterface::class);
        $this->assertInstanceOf(Foo::class, $foo);
    }

    public function testResolveClassWithDependency(): void
    {
        $bar = $this->container->get(Bar::class);
        $this->assertInstanceOf(Bar::class, $bar);
    }
}