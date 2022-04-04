<?php
declare(strict_types=1);

namespace PTS\Events;

use PTS\Events\Filter\FilterEmitterInterface;

trait EventBusTrait
{
    protected ?EventEmitterInterface $events = null;
    protected ?FilterEmitterInterface $filters = null;

    public function setEvents(EventEmitterInterface $events): void
    {
        $this->events = $events;
    }

    public function setFilters(FilterEmitterInterface $filters): void
    {
        $this->filters = $filters;
    }

    public function filter(string $name, mixed $value, array $arguments = []): mixed
    {
        return $this->filters ? $this->filters->emit($name, $value, $arguments) : $value;
    }

    public function emit(string $name, array $arguments = []): void
    {
        $this->events?->emit($name, $arguments);
    }

    public function emitArgs(string $name, array $arguments = []): void
    {
        $this->events?->emitArgs($name, $arguments);
    }

    public function emitNoArgs(string $name): void
    {
        $this->events?->emitNoArgs($name);
    }
}
