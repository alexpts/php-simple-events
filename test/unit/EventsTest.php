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

        $this->assertCount(0, $listeners1);
        $this->assertCount(1, $listeners2);
    }

    public function testSimpleEvent()
    {
        $this->events->on('some:event', [$this, 'customEventHandler']);
        $this->assertNull($this->buffer);
        $this->events->emit('some:event');
        $this->assertEquals('Work', $this->buffer);
    }

    public function testEventWithoutListeners()
    {
        $this->events->emit('some:event');
        $this->assertNull($this->buffer);
    }

    public function testClosureHandler()
    {
        $handler = \Closure::bind(function() {
            $this->buffer = 'closure';
        }, $this, get_class($this));

        $this->events->on('name', $handler);
        $this->events->emit('name');

        $this->assertEquals('closure', $this->buffer);
    }

    public function testChain()
    {
        $expected = __NAMESPACE__ . '\Events';
        $this->assertInstanceOf($expected, $this->events->on('some', [$this, 'customEventHandler']));
        $this->assertInstanceOf($expected, $this->events->emit('some'));
        $this->assertInstanceOf($expected, $this->events->off('some', [$this, 'customEventHandler']));
        $this->assertInstanceOf($expected, $this->events->emit('some'));
    }
}