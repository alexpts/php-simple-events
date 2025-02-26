<?php
declare(strict_types=1);

namespace PTS\Events\Test\Filter;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PTS\Events\Filter\FilterEmitterInterface;
use PTS\Events\Filter\FilterExtraArgsEmitter;
use PTS\Events\StopPropagation;

class FiltersExtraArgsEmitterTest extends TestCase
{

    protected FilterEmitterInterface $filters;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filters = new FilterExtraArgsEmitter;
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

    /**
     * @param string $method
     * @return void
     */
    #[DataProvider('stopPropagationDataProvider')]
    public function testStopPropagation(string $method): void
    {
        $rawTitle = '  Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim');
        $this->filters->on('before_output_title', static function ($value) {
            throw (new StopPropagation)->setValue($value);
        });
        $this->filters->on('before_output_title', [$this, 'customFilterHandler']);
        $title = $this->filters->{$method}('before_output_title', $rawTitle);

        self::assertEquals('Hello world!!!', $title);
    }

    public static function stopPropagationDataProvider(): array
    {
        return [
            ['emit'],
            ['emitNoArgs'],
        ];
    }

    public function testOnceHandler(): void
    {
        $rawTitle = '  Hello world!!!  ';
        $this->filters->once('before_output_title', 'trim');
        $title = $this->filters->emit('before_output_title', $rawTitle);
        self::assertEquals('Hello world!!!', $title);

        $title = $this->filters->emit('before_output_title', $rawTitle);
        self::assertEquals($rawTitle, $title);

        // noArgs
        $this->filters->once('before_output_title', 'trim');
        $title = $this->filters->emitNoArgs('before_output_title', $rawTitle);
        self::assertEquals('Hello world!!!', $title);

        $title = $this->filters->emitNoArgs('before_output_title', $rawTitle);
        self::assertEquals($rawTitle, $title);
    }

    public function testDifferentCountArguments(): void
    {
        $handler = Closure::bind(function(int $value, ...$args) {
            return $value + array_sum($args);
        }, $this, get_class($this));

        $this->filters->on('some:event', $handler);

        $actual = $this->filters->emit('some:event', 2, []);
        static::assertEquals(2, $actual);

        $actual = $this->filters->emit('some:event', 2, [2]);
        static::assertEquals(4, $actual);

        $actual = $this->filters->emit('some:event', 2, [2, 4]);
        static::assertEquals(8, $actual);

        $actual = $this->filters->emit('some:event', 2, [2, 4, 1]);
        static::assertEquals(9, $actual);

        $actual = $this->filters->emit('some:event', 2, [2, 4, 1, 1]);
        static::assertEquals(10, $actual);
    }

    public function testEmitNoArgs(): void
    {
        $handler = Closure::bind(function(string $value, ?string $extra) {
           return $extra ?? $value;
        }, $this, get_class($this));

        $this->filters->on('some:event', $handler, 50, ['extraArg']);

        $actual = $this->filters->emitNoArgs('some:event', 'alex');
        static::assertEquals('extraArg', $actual);
    }
}
