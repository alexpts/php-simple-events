<?php

namespace test\unit;

use Closure;
use PHPUnit\Framework\TestCase;
use PTS\Events\EventEmitter;
use PTS\Events\EventEmitterInterface;
use PTS\Events\StopPropagation;
use stdClass;

class EventsTest extends TestCase
{

    protected EventEmitter $events;
    /** @var mixed */
    protected $buffer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->events = new EventEmitter;
        $this->buffer = null;
    }

    public function customEventHandler(): void
    {
        $this->buffer = 'Work';
    }

    public function testGetListeners(): void
    {
        static::assertCount(0, $this->events->listeners());

        $this->events->on('some:event', 'trim');
        static::assertCount(1, $this->events->listeners());
        static::assertCount(1, $this->events->listeners('some:event'));
        static::assertCount(0, $this->events->listeners('not:defined'));
    }

    public function testSimpleEvent(): void
    {
        $this->events->on('some:event', [$this, 'customEventHandler']);
        static::assertNull($this->buffer);

        $this->events->emit('some:event');
        static::assertEquals('Work', $this->buffer);
    }

    public function testEventWithoutListeners(): void
    {
        $this->events->emit('some:event');
        static::assertNull($this->buffer);
    }

    public function testClosureHandler(): void
    {
        $handler = Closure::bind(function () {
            $this->buffer = 'closure';
        }, $this, get_class($this));

        $this->events->on('name', $handler);
        $this->events->emit('name');

        static::assertEquals('closure', $this->buffer);
    }

    public function testChain(): void
    {
        $expected = EventEmitterInterface::class;
        static::assertInstanceOf($expected, $this->events->on('some', [$this, 'customEventHandler']));
        static::assertInstanceOf($expected, $this->events->off('some', [$this, 'customEventHandler']));
    }

    public function testStopPropagation(): void
    {
        $handler = Closure::bind(function () {
            $this->buffer = 'closure';
            throw new StopPropagation;
        }, $this, get_class($this));

        $handler2 = Closure::bind(function () {
            $this->buffer = 'closure2';
        }, $this, get_class($this));

        $this->events->on('name', $handler);
        $this->events->on('name', $handler2);
        $this->events->emit('name');

        static::assertEquals('closure', $this->buffer);
    }

    public function testEventNames(): void
    {
        static::assertCount(0, $this->events->eventNames());

        $this->events->on('some:event', 'trim');
        $names = $this->events->eventNames();
        static::assertSame(['some:event'], $names);
    }

    public function testOnce(): void
    {
        $obj = new stdClass;
        $obj->count = 0;

        $this->events->once('once:test', function (stdClass $obj) {
            $obj->count++;
        });

        static::assertCount(1, $this->events->listeners());
        $this->events->emit('once:test', [$obj]);
        static::assertCount(0, $this->events->listeners());
        static::assertSame(1, $obj->count);
    }
}
