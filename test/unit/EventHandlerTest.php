<?php

namespace test\unit;

use PHPUnit\Framework\TestCase;
use PTS\Events\EventHandler;

class EventHandlerTest extends TestCase
{

    public function testIsSame(): void
    {
        $callable1 = fn() => 1;
        $callable2 = fn() => 2;

        $listener = new EventHandler($callable1, 0);
        static::assertTrue($listener->isSame($callable1));
        static::assertTrue($listener->isSame($callable1, 0));
        static::assertFalse($listener->isSame($callable1, 10));
        static::assertFalse($listener->isSame($callable2));
    }
}
