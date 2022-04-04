<?php
declare(strict_types=1);

namespace test\unit;

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\OutputMode;
use PhpBench\Benchmark\Metadata\Annotations\OutputTimeUnit;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Subject;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use PTS\Events\EventEmitter;
use PTS\Events\Filter\FilterEmitter;

class FilterBench
{
    protected FilterEmitter $filters;

    public function init()
    {
        $this->filters = new FilterEmitter;
        $this->filters->on('some.filter', static fn($i) => $i++);
    }
    
    /**
     * @Subject event emit
     * @Revs(100000)
     * @Iterations(20)
     * @BeforeMethods({"init"})
     * @OutputTimeUnit("microseconds", precision=3)
     * @OutputMode("throughput")
     * @Warmup(1)
     */
    public function emit(): void
    {
        $this->filters->emit('some.filter', 1);
    }

    /**
     * @Subject event emit no args
     * @Revs(100000)
     * @Iterations(20)
     * @BeforeMethods({"init"})
     * @OutputTimeUnit("microseconds", precision=3)
     * @OutputMode("throughput")
     * @Warmup(1)
     */
    public function emitNoArgs(): void
    {
        $this->filters->emitNoArgs('some.event', 1);
    }

    /**
     * @Subject event emitArgs
     * @Revs(100000)
     * @Iterations(20)
     * @BeforeMethods({"init"})
     * @OutputTimeUnit("microseconds", precision=3)
     * @OutputMode("throughput")
     * @Warmup(1)
     */
    public function emitArgs(): void
    {
        $this->filters->emitArgs('some.event', 2, ['sister', 14]);
    }

    /**
     * @Subject event emitArgs
     * @Revs(100000)
     * @Iterations(20)
     * @BeforeMethods({"init"})
     * @OutputTimeUnit("microseconds", precision=3)
     * @OutputMode("throughput")
     * @Warmup(1)
     */
    public function emitNoExtraArgs(): void
    {
        $this->filters->emitNoExtraArgs('some.event', 2);
    }

    /**
     * @Subject event emitArgs #2
     * @Revs(100000)
     * @Iterations(20)
     * @BeforeMethods({"init"})
     * @OutputTimeUnit("microseconds", precision=3)
     * @OutputMode("throughput")
     * @Warmup(1)
     */
    public function emitNoExtraArgs2(): void
    {
        $this->filters->emitNoExtraArgs('some.event', 2, ['sister', 14]);
    }
}
