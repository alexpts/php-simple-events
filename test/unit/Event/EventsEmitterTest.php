<?php
declare(strict_types=1);

namespace PTS\Events\Test\Event;

use Closure;
use PHPUnit\Framework\TestCase;
use PTS\Events\EventEmitter;
use PTS\Events\EventEmitterInterface;
use PTS\Events\StopPropagation;
use stdClass;

class EventsEmitterTest extends TestCase
{

    protected EventEmitterInterface $events;
    protected mixed $buffer;

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

    public function testDifferentCountArguments(): void
    {
        $handler = Closure::bind(function(...$args) {
            $this->buffer = array_sum($args);
        }, $this, get_class($this));

        $this->events->on('some:event', $handler);

        $this->events->emit('some:event', []);
        static::assertEquals(0, $this->buffer);

        $this->events->emit('some:event', [2]);
        static::assertEquals(2, $this->buffer);

        $this->events->emit('some:event', [2, 4]);
        static::assertEquals(6, $this->buffer);

        $this->events->emit('some:event', [2, 4, 1]);
        static::assertEquals(7, $this->buffer);

        $this->events->emit('some:event', [2, 4, 1, 1]);
        static::assertEquals(8, $this->buffer);
    }

    public function testEmitNoArgs(): void
    {
        $handler = Closure::bind(function(string $title = 'default') {
            $this->buffer = $title;
        }, $this, get_class($this));

        $this->events->on('some:event', $handler, 50, ['extraArg']);

        $this->events->emitNoArgs('some:event');
        static::assertEquals('default', $this->buffer);
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
        static::assertInstanceOf($expected, $this->events->once('some', [$this, 'customEventHandler']));
        static::assertInstanceOf($expected, $this->events->off('some', [$this, 'customEventHandler']));
    }

    /**
     * @return void
     * @dataProvider stopPropagationDataProvider
     */
    public function testStopPropagation(string $method): void
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

        $this->events->{$method}('name');

        static::assertEquals('closure', $this->buffer);
    }

    public function stopPropagationDataProvider(): array
    {
        return [
          ['emit'] ,
          ['emitNoArgs']
        ];
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

        $this->events->once('once:test', fn(stdClass $obj) => $obj->count++);

        static::assertCount(1, $this->events->listeners());
        $this->events->emit('once:test', [$obj]);
        static::assertCount(0, $this->events->listeners());
        static::assertSame(1, $obj->count);

        $this->events->once('once:test2', function() { });
        static::assertCount(1, $this->events->listeners());
        $this->events->emitNoArgs('once:test2');
        static::assertCount(0, $this->events->listeners());
    }
}
