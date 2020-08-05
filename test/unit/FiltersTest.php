<?php

namespace test\unit;

use PHPUnit\Framework\TestCase;
use PTS\Events\Filter\FilterEmitter;
use PTS\Events\Filter\FilterEmitterInterface;
use PTS\Events\StopPropagation;

class FiltersTest extends TestCase
{

    protected FilterEmitterInterface $filters;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filters = new FilterEmitter;
    }

    public function customFilterHandler(string $value, int $length = 4): string
    {
        return substr($value, 0, $length);
    }

    public function testSimpleFilter(): void
    {
        $title = ' Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim');
        $title = $this->filters->emit('before_output_title', $title);

        self::assertEquals(trim($title), $title);
    }

    public function testFilterWithoutListeners(): void
    {
        $rawTitle = ' Hello world!!!  ';
        $title = $this->filters->emit('before_output_title', $rawTitle);

        self::assertEquals($rawTitle, $title);
    }

    public function testFilterWithExtraEmitArguments(): void
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', [$this, 'customFilterHandler']);
        $title = $this->filters->emit('before_output_title', $rawTitle, [5]);

        self::assertEquals('Hello', $title);
    }

    public function testFilterWithExtraOnArguments(): void
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', [$this, 'customFilterHandler'], 50, [5]);
        $title = $this->filters->emit('before_output_title', $rawTitle);

        self::assertEquals('Hello', $title);
    }

    public function testOffHandlerWithPriority(): void
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim', 30);
        $this->filters->off('before_output_title', 'trim', 30);
        $title = $this->filters->emit('before_output_title', $rawTitle);

        self::assertEquals($rawTitle, $title);
        self::assertCount(0, $this->filters->listeners());
    }

    public function testOffHandlerWithoutPriority(): void
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim');
        $this->filters->off('before_output_title', 'trim');
        $title = $this->filters->emit('before_output_title', $rawTitle);

        self::assertEquals($rawTitle, $title);
        self::assertCount(0, $this->filters->listeners());
    }

    public function testOffAllHandlers(): void
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim');
        $this->filters->off('before_output_title');
        $title = $this->filters->emit('before_output_title', $rawTitle);

        self::assertEquals($rawTitle, $title);
        self::assertCount(0, $this->filters->listeners());
    }

    public function testOrderHandler(): void
    {
        $rawTitle = '  Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim', 30);
        $this->filters->on('before_output_title', [$this, 'customFilterHandler'], 20);
        $title = $this->filters->emit('before_output_title', $rawTitle);

        self::assertEquals('Hell', $title);
    }

    public function test2OrderHandler(): void
    {
        $rawTitle = '  Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim', 30);
        $this->filters->on('before_output_title', [$this, 'customFilterHandler'], 40);
        $title = $this->filters->emit('before_output_title', $rawTitle);

        self::assertEquals('He', $title);
    }

    public function testChain(): void
    {
        self::assertInstanceOf(FilterEmitterInterface::class, $this->filters->on('some', 'trim'));
        self::assertInstanceOf(FilterEmitterInterface::class, $this->filters->off('some', 'trim'));
    }

    public function testStopPropagation(): void
    {
        $rawTitle = '  Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim');
        $this->filters->on('before_output_title', static function ($value) {
            throw (new StopPropagation)->setValue($value);
        });
        $this->filters->on('before_output_title', [$this, 'customFilterHandler']);
        $title = $this->filters->emit('before_output_title', $rawTitle);

        self::assertEquals('Hello world!!!', $title);
    }

    public function testOnceHandler(): void
    {
        $rawTitle = '  Hello world!!!  ';
        $this->filters->once('before_output_title', 'trim');
        $this->filters->emit('before_output_title', $rawTitle);
        $title = $this->filters->emit('before_output_title', $rawTitle);

        self::assertEquals($rawTitle, $title);
    }
}
