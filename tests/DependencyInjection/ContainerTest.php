<?php


namespace Tests\DependencyInjection;

use App\DependencyInjection\Container;
use App\DependencyInjection\Exception\ContainerException;
use App\DependencyInjection\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\DependencyInjection\Utils\Interfaces\NoScalarFourInterface;
use Tests\DependencyInjection\Utils\Interfaces\NoScalarOneInterface;
use Tests\DependencyInjection\Utils\Interfaces\NoScalarThreeInterface;
use Tests\DependencyInjection\Utils\Interfaces\NoScalarTwoInterface;
use Tests\DependencyInjection\Utils\NoDependency\NoDependency;
use Tests\DependencyInjection\Utils\NoScalarDependency\NoScalarFour;
use Tests\DependencyInjection\Utils\NoScalarDependency\NoScalarOne;
use Tests\DependencyInjection\Utils\NoScalarDependency\NoScalarThree;
use Tests\DependencyInjection\Utils\NoScalarDependency\NoScalarTwo;
use Tests\DependencyInjection\Utils\ScalarDependency\ScalarOne;

class ContainerTest extends TestCase
{

    /** @var Container $container */
    private $container;

    protected function setUp(): void
    {
       $this->container = new Container();
    }

    public function testNotFoundExceptionWithInvalidClassName(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectErrorMessage("Provide a valid class or interface.");
        $this->container->get("test");
    }

    public function testResolveClassWithNoDependency(): void
    {
        $obj = $this->container->get(NoDependency::class);
        $this->assertInstanceOf(NoDependency::class, $obj);
    }

    public function testSingleton(): void
    {
        $instance1 = $this->container->get(NoDependency::class);
        $instance2 = $this->container->get(NoDependency::class);
        $this->assertEquals(spl_object_id($instance1), spl_object_id($instance2));
    }

    public function testNotFoundExceptionWithMissingParameters(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectErrorMessage("Provide value for parameter arg1.");
        $this->container->get(ScalarOne::class);
    }
    public function testContainerExceptionWhenResolveClassWithInvalidParameterType()
    {
        $this->container
            ->addParameter("arg1", "test")
            ->addParameter("arg2", 42)
            ->addParameter("arg3", 1.25)
            ->addParameter("arg4", [1,2,3])
            ->addParameter("arg5", true)
            ->addParameter("arg6", function() {})
            ->addParameter("arg7", new \DateTimeImmutable())
            ->addParameter("arg8", "update default value");

        $this->expectException(ContainerException::class);
        $this->expectErrorMessage(ScalarOne::class . "::__construct(), parameter arg7 expected DateTime given DateTimeImmutable");
        $this->container->get(ScalarOne::class);
    }

    public function testResolveClassWithOnlyScalarDependencies(): void
    {
        $this->container
            ->addParameter("arg1", "test")
            ->addParameter("arg2", 42)
            ->addParameter("arg3", 1.25)
            ->addParameter("arg4", [1,2,3])
            ->addParameter("arg5", true)
            ->addParameter("arg6", function() {})
            ->addParameter("arg7", new \DateTime())
            ->addParameter("arg8", "update default value")
            ->addParameter("arg9", 15);
        $obj = $this->container->get(ScalarOne::class);
        $this->assertInstanceOf(ScalarOne::class, $obj);
    }

    public function testResolveClassWithDependency(): void
    {
        $obj = $this->container->get(NoScalarOne::class);
        $this->assertInstanceOf(NoScalarOne::class, $obj);
    }

    public function testNotFoundExceptionWithMissingAlias(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectErrorMessage("Alias ". NoScalarTwoInterface::class ." not found.");
        $this->container->get(NoScalarTwoInterface::class);
    }

    public function testContainerExceptionClassNotImplementInterface(): void
    {
        $this->container->addAlias(NoScalarTwoInterface::class, NoScalarTwo::class);
        $this->container->addAlias(NoScalarOneInterface::class, NoScalarOne::class);
        $this->expectException(ContainerException::class);
        $this->expectErrorMessage(NoScalarTwo::class . "::__construct(), parameter arg expected ".NoScalarOneInterface::class." given ".NoScalarOne::class);
        $this->container->get(NoScalarTwoInterface::class);
    }

    public function testResolveInterface()
    {
        $this->container->addAlias(NoScalarThreeInterface::class, NoScalarThree::class);
        $this->container->addAlias(NoScalarFourInterface::class, NoScalarFour::class);

        $obj = $this->container->get(NoScalarFourInterface::class);
        $this->assertInstanceOf(NoScalarFour::class, $obj);
    }
}