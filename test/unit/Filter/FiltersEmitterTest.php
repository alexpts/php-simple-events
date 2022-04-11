<?php
declare(strict_types=1);

namespace PTS\Events\Test\Filter;

use Closure;
use PHPUnit\Framework\TestCase;
use PTS\Events\Filter\FilterEmitter;
use PTS\Events\Filter\FilterEmitterInterface;
use PTS\Events\StopPropagation;

class FiltersEmitterTest extends FiltersExtraArgsEmitterTest
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->filters = new FilterEmitter;
    }

    public function testFilterWithExtraOnArguments(): void
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', [$this, 'customFilterHandler'], 50, [10]);
        $title = $this->filters->emit('before_output_title', $rawTitle);

        self::assertEquals('Hell', $title, 'must skip extra args from handler');
    }

    public function testEmitNoArgs(): void
    {
        $handler = Closure::bind(function(string $value, ?string $extra = null) {
            return $extra ?? $value;
        }, $this, get_class($this));

        $this->filters->on('some:event', $handler, 50, ['extraArg']);

        $actual = $this->filters->emitNoArgs('some:event', 'alex');
        static::assertEquals('alex', $actual);
    }
}
