<?php

namespace Test;

use PHPUnit\Framework\TestCase;

class EmitterTest extends TestCase
{
    public function testAreWorking(): void
    {
        $this->assertEquals(2, 1+1);
    }
}