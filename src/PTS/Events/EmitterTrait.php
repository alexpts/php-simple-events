<?php
declare(strict_types=1);

namespace PTS\Events;

trait EmitterTrait
{
    /** @var EventsInterface|null */
    protected $events;
    /** @var FiltersInterface|null */
    protected $filters;

    public function setEvents(EventsInterface $events): void
    {
        $this->events = $events;
    }

    public function setFilters(FiltersInterface $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function filter(string $name, $value)
    {
        return $this->filters ? $this->filters->filter($name, $value) : $value;
    }

    public function emit(string $name, array $arguments = []): void
    {
        $this->events && $this->events->emit($name, $arguments);
    }
}
