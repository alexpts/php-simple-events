<?php

namespace test\unit;

use PHPUnit\Framework\TestCase;
use PTS\Events\Events;
use PTS\Events\EventsInterface;
use PTS\Events\StopPropagation;

class EventsTest extends TestCase
{
    /** @var Events */
    protected $events;
    /** @var mixed */
    protected $buffer;

    protected function setUp()
    {
        $this->events = new Events;
        $this->buffer = null;
    }

    public function customEventHandler(): void
    {
        $this->buffer = 'Work';
    }

    public function testGetListeners(): void
    {
        $listeners1 = $this->events->getListeners();
        $this->events->on('some:event', 'trim');
        $listeners2 = $this->events->getListeners();

        self::assertCount(0, $listeners1);
        self::assertCount(1, $listeners2);
    }

    public function testSimpleEvent(): void
    {
        $this->events->on('some:event', [$this, 'customEventHandler']);
        self::assertNull($this->buffer);

        $this->events->emit('some:event');
        self::assertEquals('Work', $this->buffer);
    }

    public function testEventWithoutListeners(): void
    {
        $this->events->emit('some:event');
        self::assertNull($this->buffer);
    }

    public function testClosureHandler(): void
    {
        $handler = \Closure::bind(function () {
            $this->buffer = 'closure';
        }, $this, get_class($this));

        $this->events->on('name', $handler);
        $this->events->emit('name');

        self::assertEquals('closure', $this->buffer);
    }

    public function testChain(): void
    {
        $expected = EventsInterface::class;
        self::assertInstanceOf($expected, $this->events->on('some', [$this, 'customEventHandler']));
        self::assertInstanceOf($expected, $this->events->emit('some'));
        self::assertInstanceOf($expected, $this->events->off('some', [$this, 'customEventHandler']));
        self::assertInstanceOf($expected, $this->events->emit('some'));
    }

    public function testStopPropagation(): void
    {
        $handler = \Closure::bind(function () {
            $this->buffer = 'closure';
            throw new StopPropagation;
        }, $this, get_class($this));

        $handler2 = \Closure::bind(function () {
            $this->buffer = 'closure2';
        }, $this, get_class($this));

        $this->events->on('name', $handler);
        $this->events->on('name', $handler2);
        $this->events->emit('name');

        self::assertEquals('closure', $this->buffer);
    }
}
