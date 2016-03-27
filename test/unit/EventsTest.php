<?php
namespace PTS\Events;

class EventsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Events */
    protected $events;
    /** @var mixed */
    protected $buffer;

    protected function setUp()
    {
        $this->events = new Events();
        $this->buffer = null;
    }

    public function customEventHandler()
    {
        $this->buffer = 'Work';
    }

    public function testGetListeners()
    {
        $listeners1 = $this->events->getListeners();
        $this->events->on('some:event', 'trim');
        $listeners2 = $this->events->getListeners();

        self::assertCount(0, $listeners1);
        self::assertCount(1, $listeners2);
    }

    public function testSimpleEvent()
    {
        $this->events->on('some:event', [$this, 'customEventHandler']);
        self::assertNull($this->buffer);

        $this->events->emit('some:event');
        self::assertEquals('Work', $this->buffer);
    }

    public function testEventWithoutListeners()
    {
        $this->events->emit('some:event');
        self::assertNull($this->buffer);
    }

    public function testClosureHandler()
    {
        $handler = \Closure::bind(function() {
            $this->buffer = 'closure';
        }, $this, get_class($this));

        $this->events->on('name', $handler);
        $this->events->emit('name');

        self::assertEquals('closure', $this->buffer);
    }

    public function testChain()
    {
        $expected = __NAMESPACE__ . '\EventsInterface';
        self::assertInstanceOf($expected, $this->events->on('some', [$this, 'customEventHandler']));
        self::assertInstanceOf($expected, $this->events->emit('some'));
        self::assertInstanceOf($expected, $this->events->off('some', [$this, 'customEventHandler']));
        self::assertInstanceOf($expected, $this->events->emit('some'));
    }

    public function testStopPropagation()
    {
        $handler = \Closure::bind(function() {
            $this->buffer = 'closure';
            throw new StopPropagation;
        }, $this, get_class($this));

        $handler2 = \Closure::bind(function() {
            $this->buffer = 'closure2';
        }, $this, get_class($this));

        $this->events->on('name', $handler);
        $this->events->on('name', $handler2);
        $this->events->emit('name');

        self::assertEquals('closure', $this->buffer);
    }
}