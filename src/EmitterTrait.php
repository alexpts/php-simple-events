<?php
declare(strict_types=1);

namespace PTS\Events;

trait EmitterTrait
{
    protected ?EventsInterface $events = null;
    protected ?FiltersInterface $filters = null;

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
     * @param mixed $arguments
     *
     * @return mixed
     */
    public function filter(string $name, $value, array $arguments = [])
    {
        return $this->filters ? $this->filters->filter($name, $value, $arguments) : $value;
    }

    public function emit(string $name, array $arguments = []): void
    {
        $this->events && $this->events->emit($name, $arguments);
    }
}
