<?php

namespace Tests;

use App\Observer\Emitter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use ReflectionClass;

class EmitterTest extends TestCase
{
    /**
     * @var Emitter $emitter
     */
    private $emitter;

    protected function setUp(): void
    {
        $reflexionClass = new ReflectionClass(Emitter::class);
        $property = $reflexionClass->getProperty("_instance");
        $property->setAccessible(true);
        $property->setValue(null, null);
        $property->setAccessible(false);

        $this->emitter = Emitter::getInstance();
    }

    public function testEmitterInstanceIsSingleton(): void
    {
        $emitter1 = Emitter::getInstance();
        $emitter2 = Emitter::getInstance();

        $this->assertSame($emitter1, $emitter2);
    }

    public function testEmitEventTriggerCallable(): void
    {
        $listener = $this->mockListener();

        $this->emitter->on("Test.event", [$listener, "onSend"]);

        $listener->expects($this->once())->method("onSend")->with($this->stringContains('John'));

        $this->emitter->emit("Test.event", "John");
    }

    public function testEmitEventTriggerCallableManyTime(): void
    {
        $listener = $this->mockListener();

        $this->emitter->on("Test.event", [$listener, "onSend"]);

        $listener->expects($this->exactly(2))->method("onSend")->with($this->stringContains('John'));

        $this->emitter->emit("Test.event", "John");
        $this->emitter->emit("Test.event", "John");
    }

    public function testEmitEventTriggerCallableWithPriority(): void
    {
        $listener = $this->mockListener(["onFirst", "onSecond", "onLast"]);

        $this->emitter->on("Test.priority", [$listener, "onLast"]);
        $this->emitter->on("Test.priority", [$listener, "onSecond"], 1);
        $this->emitter->on("Test.priority", [$listener, "onFirst"], 100);

        $flag = null;

        $listener->expects($this->once())->method("onFirst")->willReturnCallback(function () use (&$flag) {
            $this->assertNull($flag);
            $flag = "first";
        });
        $listener->expects($this->once())->method("onSecond")->willReturnCallback(function () use (&$flag) {
            $this->assertEquals("first", $flag);
            $flag = "second";
        });
        $listener->expects($this->once())->method("onLast")->willReturnCallback(function () use (&$flag) {
            $this->assertEquals("second", $flag);
        });

        $this->emitter->emit("Test.priority");
    }

    private function mockListener(array $methods = ["onSend"]): MockObject
    {
        return $this->getMockBuilder(stdClass::class)->addMethods($methods)->getMock();
    }
}