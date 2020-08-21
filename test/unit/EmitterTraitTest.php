<?php

namespace test\unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PTS\Events\EventBusTrait;
use PTS\Events\EventEmitter;
use PTS\Events\EventEmitterInterface;
use PTS\Events\Filter\FilterEmitter;
use PTS\Events\Filter\FilterEmitterInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class EmitterTraitTest extends TestCase
{
    /** @var EventEmitterInterface */
    protected $events;
    /** @var FilterEmitterInterface */
    protected $filters;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->events = new EventEmitter;
        $this->filters = new FilterEmitter;
    }

    /**
     * @return EventBusTrait|MockObject
     */
    protected function getTraitMock()
    {
        return $this->getMockBuilder(EventBusTrait::class)->getMockForTrait();
    }

    /**
     * @param mixed $mock
     * @param string $prop
     * @return ReflectionProperty
     * @throws ReflectionException
     */
    protected function getTraitProperty($mock, string $prop): ReflectionProperty
    {
        $class = new ReflectionClass($mock);
        $prop = $class->getProperty($prop);
        $prop->setAccessible(true);

        return $prop;
    }

    /**
     * @throws ReflectionException
     */
    public function testSetEvent(): void
    {
        $emitter = $this->getTraitMock();
        $emitter->setEvents($this->events);

        $prop = $this->getTraitProperty($emitter, 'events');
        $actual = $prop->getValue($emitter);

        self::assertInstanceOf(EventEmitterInterface::class, $actual);
    }

    /**
     * @throws ReflectionException
     */
    public function testSetFilters(): void
    {
        $emitter = $this->getTraitMock();
        $emitter->setFilters($this->filters);

        $prop = $this->getTraitProperty($emitter, 'filters');
        $actual = $prop->getValue($emitter);

        self::assertInstanceOf(FilterEmitterInterface::class, $actual);
    }

    /**
     * @param string $input
     * @param string $expected
     * @param bool $hasFilter
     *
     * @dataProvider dataProviderTestFilter
     */
    public function testFilter(string $input, string $expected, bool $hasFilter = true): void
    {
        $this->filters->on('some.filter', 'trim');
        $emitter = $this->getTraitMock();

        if ($hasFilter) {
            $emitter->setFilters($this->filters);
        }

        $actual = $emitter->filter('some.filter', $input);
        self::assertSame($expected, $actual);
    }

    public function dataProviderTestFilter(): array
    {
        return [
           [ ' a ', 'a', true],
           [ ' a ', ' a ', false],
        ];
    }

    public function testEmit(): void
    {
        $val = (object)['count' => 0];
        $this->events->on('some.event', function (object $value) {
            $value->count++;
        });

        $emitter = $this->getTraitMock();

        $emitter->emit('some.event', [$val]);
        self::assertSame(0, $val->count);

        $emitter->setEvents($this->events);
        $emitter->emit('some.event', [$val]);
        self::assertSame(1, $val->count);
    }
}
