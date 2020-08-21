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

    /**
     * @param string $name
     * @param mixed $value
     * @param array $arguments
     *
     * @return mixed $value
     */
    public function filter(string $name, $value, array $arguments = [])
    {
        return $this->filters ? $this->filters->emit($name, $value, $arguments) : $value;
    }

    public function emit(string $name, array $arguments = []): void
    {
        $this->events && $this->events->emit($name, $arguments);
    }
}
