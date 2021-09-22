<?php

namespace Test;

use App\Observer\Emitter;
use PHPUnit\Framework\TestCase;

class EmitterTest extends TestCase
{
    public function testEmitterInstanceIsSingleton(): void
    {
        $emitter1 = Emitter::getInstance();
        $emitter2 = Emitter::getInstance();

        $this->assertSame($emitter1, $emitter2);
    }
}