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

    private function mockListener(): MockObject
    {
        return $this->getMockBuilder(stdClass::class)->addMethods(["onSend"])->getMock();
    }
}