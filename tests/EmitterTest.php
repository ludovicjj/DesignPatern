<?php

namespace Tests;

use App\Observer\DuplicatedEventException;
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
        $event = $this->mockEvent();

        $this->emitter->on("Test.event", [$event, "onSend"]);

        $event->expects($this->once())->method("onSend")->with($this->stringContains('John'));

        $this->emitter->emit("Test.event", "John");
    }

    public function testEmitEventTriggerCallableManyTime(): void
    {
        $event = $this->mockEvent();

        $this->emitter->on("Test.event", [$event, "onSend"]);

        $event->expects($this->exactly(2))->method("onSend")->with($this->stringContains('John'));

        $this->emitter->emit("Test.event", "John");
        $this->emitter->emit("Test.event", "John");
    }

    public function testEmitEventTriggerCallableWithPriority(): void
    {
        $event = $this->mockEvent(["onFirst", "onSecond", "onLast"]);

        $this->emitter->on("Test.priority", [$event, "onLast"]);
        $this->emitter->on("Test.priority", [$event, "onSecond"], 1);
        $this->emitter->on("Test.priority", [$event, "onFirst"], 100);

        $flag = null;

        $event->expects($this->once())->method("onFirst")->willReturnCallback(function () use (&$flag) {
            $this->assertNull($flag);
            $flag = "first";
        });
        $event->expects($this->once())->method("onSecond")->willReturnCallback(function () use (&$flag) {
            $this->assertEquals("first", $flag);
            $flag = "second";
        });
        $event->expects($this->once())->method("onLast")->willReturnCallback(function () use (&$flag) {
            $this->assertEquals("second", $flag);
        });

        $this->emitter->emit("Test.priority");
    }

    public function testEmitEventTriggerCallableOnce(): void
    {
        $event = $this->mockEvent();
        $listener = $this->emitter->once("Test.once", [$event, "onSend"]);
        $event->expects($this->once())->method("onSend");

        $this->emitter->emit("Test.once");
        $this->emitter->emit("Test.once");
        $this->emitter->emit("Test.once");
        $this->emitter->emit("Test.once");

        $this->assertEquals(1, $listener->getCalls());
    }

    public function testCalls(): void
    {
        $event = $this->mockEvent();
        $listener = $this->emitter->on("Test.once", [$event, "onSend"]);
        $event->expects($this->exactly(4))->method("onSend");

        $this->emitter->emit("Test.once");
        $this->emitter->emit("Test.once");
        $this->emitter->emit("Test.once");
        $this->emitter->emit("Test.once");

        $this->assertEquals(4, $listener->getCalls());
    }

    public function testStopPropagation(): void
    {
        $event = $this->mockEvent(["onFirst", "onLast"]);
        $this->emitter->on("Test.propagation", [$event, "onFirst"], 0, true);
        $this->emitter->on("Test.propagation", [$event, "onLast"]);

        $event->expects($this->once())->method("onFirst");
        $event->expects($this->never())->method("onLast");

        $this->emitter->emit("Test.propagation");
    }

    public function testStopPropagationWithPriority(): void
    {
        $event = $this->mockEvent(["onFirst", "onSecond", "onLast"]);

        $this->emitter->on("Test.propagation", [$event, "onFirst"], 0, true);
        $this->emitter->on("Test.propagation", [$event, "onSecond"], 10, false);
        $this->emitter->on("Test.propagation", [$event, "onLast"]);

        $event->expects($this->exactly(2))->method("onFirst");
        $event->expects($this->exactly(2))->method("onSecond");
        $event->expects($this->never())->method("onLast");

        $this->emitter->emit("Test.propagation");
        $this->emitter->emit("Test.propagation");
    }

    public function testRegisterEventWithSameCallableTwice(): void
    {
        $event = $this->mockEvent();

        $this->emitter->on("Test.propagation", [$event, "onSend"]);
        $this->expectException(DuplicatedEventException::class);
        $this->emitter->on("Test.propagation", [$event, "onSend"]);
    }

    private function mockEvent(array $methods = ["onSend"]): MockObject
    {
        return $this->getMockBuilder(stdClass::class)->addMethods($methods)->getMock();
    }
}