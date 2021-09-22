<?php

namespace Test;

use App\Observer\Emitter;
use PHPUnit\Framework\TestCase;
use stdClass;

class EmitterTest extends TestCase
{
    public function testEmitterInstanceIsSingleton(): void
    {
        $emitter1 = Emitter::getInstance();
        $emitter2 = Emitter::getInstance();

        $this->assertSame($emitter1, $emitter2);
    }

    public function testEmitEventTriggerCallable(): void
    {
        $emitter = Emitter::getInstance();
        $listener = $this->getMockBuilder(stdClass::class)->addMethods(["onSend"])->getMock();

        $emitter->on("Test.event", [$listener, "onSend"]);

        $listener->expects($this->once())->method("onSend")->with($this->stringContains('John'));

        $emitter->emit("Test.event", "John");
    }

    public function testEmitEventTriggerCallableManyTime(): void
    {
        $emitter = Emitter::getInstance();
        $listener = $this->getMockBuilder(stdClass::class)->addMethods(["onSend"])->getMock();

        $emitter->on("Test.event", [$listener, "onSend"]);

        $listener->expects($this->exactly(2))->method("onSend")->with($this->stringContains('John'));

        $emitter->emit("Test.event", "John");
        $emitter->emit("Test.event", "John");
    }
}