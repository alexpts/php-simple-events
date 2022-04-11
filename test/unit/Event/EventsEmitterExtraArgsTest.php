<?php
declare(strict_types=1);

namespace PTS\Events\Test\Event;

use Closure;
use PHPUnit\Framework\TestCase;
use PTS\Events\EventEmitter;
use PTS\Events\EventEmitterExtraArgs;
use PTS\Events\EventEmitterInterface;
use PTS\Events\StopPropagation;
use stdClass;

class EventsEmitterExtraArgsTest extends EventsEmitterTest
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->events = new EventEmitterExtraArgs;
        $this->buffer = null;
    }

    public function testEmitNoArgs(): void
    {
        $handler = Closure::bind(function(string $title = 'default') {
            $this->buffer = $title;
        }, $this, get_class($this));

        $this->events->on('some:event', $handler, 50, ['extraArg']);

        $this->events->emitNoArgs('some:event');
        static::assertEquals('extraArg', $this->buffer);
    }
}
