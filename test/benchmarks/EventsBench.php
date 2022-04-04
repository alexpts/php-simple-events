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

class EventsBench
{
    protected EventEmitter $emitter;

    public function init()
    {
        $this->emitter = new EventEmitter;
        $this->emitter->on('some.event', static fn() => 1);
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
        $this->emitter->emit('some.event');
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
        $this->emitter->emitNoArgs('some.event');
    }

    /**
     * @Subject event emit old args
     * @Revs(100000)
     * @Iterations(20)
     * @BeforeMethods({"init"})
     * @OutputTimeUnit("microseconds", precision=3)
     * @OutputMode("throughput")
     * @Warmup(1)
     */
    public function emitOldArgs(): void
    {
        $this->emitter->emit('some.event', ['sister', 14]);
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
        $this->emitter->emitArgs('some.event', ['sister', 14]);
    }
}
