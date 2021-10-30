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

class EventsBench
{
    protected EventEmitter $emitter;

    public function init()
    {
        $this->emitter = new EventEmitter;
        $this->emitter->on('some.event', static fn() => 1);
    }
    
    /**
     * @Subject bench
     * @Revs(10000000)
     * @Iterations(10)
     * @BeforeMethods({"init"})
     * @OutputTimeUnit("microseconds", precision=3)
     * @OutputMode("throughput")
     * @Warmup(1)
     */
    public function emit(): void
    {
        $this->emitter->emit('some.event');
    }
}
