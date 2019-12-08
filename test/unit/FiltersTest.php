<?php

namespace test\unit;

use PHPUnit\Framework\TestCase;
use PTS\Events\Filters;
use PTS\Events\FiltersInterface;
use PTS\Events\StopPropagation;

class FiltersTest extends TestCase
{

    protected Filters $filters;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filters = new Filters;
    }

    public function customFilterHandler(string $value, int $length = 4): string
    {
        return substr($value, 0, $length);
    }

    public function testGetListeners(): void
    {
        $listeners1 = $this->filters->getListeners();
        $this->filters->on('some:id', 'trim');
        $listeners2 = $this->filters->getListeners();

        self::assertCount(0, $listeners1);
        self::assertCount(1, $listeners2);
    }

    public function testSimpleFilter(): void
    {
        $title = ' Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim');
        $title = $this->filters->filter('before_output_title', $title);

        self::assertEquals(trim($title), $title);
    }

    public function testFilterWithoutListeners(): void
    {
        $rawTitle = ' Hello world!!!  ';
        $title = $this->filters->filter('before_output_title', $rawTitle);

        self::assertEquals($rawTitle, $title);
    }

    public function testFilterWithExtraEmitArguments(): void
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', [$this, 'customFilterHandler']);
        $title = $this->filters->filter('before_output_title', $rawTitle, [5]);

        self::assertEquals('Hello', $title);
    }

    public function testFilterWithExtraOnArguments(): void
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', [$this, 'customFilterHandler'], 50, [5]);
        $title = $this->filters->filter('before_output_title', $rawTitle);

        self::assertEquals('Hello', $title);
    }

    public function testOffHandlerWithPriority(): void
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim', 30);
        $this->filters->off('before_output_title', 'trim', 30);
        $title = $this->filters->filter('before_output_title', $rawTitle);

        self::assertEquals($rawTitle, $title);
        self::assertCount(0, $this->filters->getListeners());
    }

    public function testOffHandlerWithoutPriority(): void
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim');
        $this->filters->off('before_output_title', 'trim');
        $title = $this->filters->filter('before_output_title', $rawTitle);

        self::assertEquals($rawTitle, $title);
        self::assertCount(0, $this->filters->getListeners());
    }

    public function testOffAllHandlers(): void
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim');
        $this->filters->off('before_output_title');
        $title = $this->filters->filter('before_output_title', $rawTitle);

        self::assertEquals($rawTitle, $title);
        self::assertCount(0, $this->filters->getListeners());
    }

    public function testOrderHandler(): void
    {
        $rawTitle = '  Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim', 30);
        $this->filters->on('before_output_title', [$this, 'customFilterHandler'], 20);
        $title = $this->filters->filter('before_output_title', $rawTitle);

        self::assertEquals('Hell', $title);
    }

    public function test2OrderHandler(): void
    {
        $rawTitle = '  Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim', 30);
        $this->filters->on('before_output_title', [$this, 'customFilterHandler'], 40);
        $title = $this->filters->filter('before_output_title', $rawTitle);

        self::assertEquals('He', $title);
    }

    public function testChain(): void
    {
        self::assertInstanceOf(FiltersInterface::class, $this->filters->on('some', 'trim'));
        self::assertInstanceOf(FiltersInterface::class, $this->filters->off('some', 'trim'));
    }

    public function testStopPropagation(): void
    {
        $rawTitle = '  Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim');
        $this->filters->on('before_output_title', static function ($value) {
            throw (new StopPropagation)->setValue($value);
        });
        $this->filters->on('before_output_title', [$this, 'customFilterHandler']);
        $title = $this->filters->filter('before_output_title', $rawTitle);

        self::assertEquals('Hello world!!!', $title);
    }

    public function testOnceHandler(): void
    {
        $rawTitle = '  Hello world!!!  ';
        $this->filters->once('before_output_title', 'trim');
        $this->filters->filter('before_output_title', $rawTitle);
        $title = $this->filters->filter('before_output_title', $rawTitle);

        self::assertEquals($rawTitle, $title);
    }
}
