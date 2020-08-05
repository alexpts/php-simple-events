<?php
declare(strict_types=1);

namespace PTS\Events;

trait EventEmitterTrait
{
    /** @var EventHandler[][] */
    protected array $listeners = [];
    /** @var bool[] */
    protected array $sorted = [];

    public function emit(string $name, array $args = []): self
    {
        $event = $this->listeners[$name] ?? null;
        if ($event === null) {
            return $this;
        }

        try {
            foreach ($this->getSortedListeners($name) as $i => $listener) {
                $handler = $listener->handler;
                $handler(...$args, ...$listener->extraArgs);
                if ($listener->once) {
                    unset($this->listeners[$name][$i]);
                    if (count($this->listeners[$name]) === 0) {
                        unset($this->listeners[$name]);
                    }
                }
            }
        } catch (StopPropagation $e) {
            return $this;
        }

        return $this;
    }

    public function listeners(string $event = null): array
    {
        if ($event === null) {
            return $this->listeners;
        }

        return $this->listeners[$event] ?? [];
    }

    public function eventNames(): array
    {
        return array_keys($this->listeners());
    }

    /**
     * @param string $name
     *
     * @return EventHandler[]
     */
    protected function getSortedListeners(string $name): array
    {
        $isSorted = $this->sorted[$name] ?? false;
        if (!$isSorted) {
            $sorted = [];

            foreach ($this->listeners[$name] as $listener) {
                $sorted[$listener->priority][] = $listener;
            }

            krsort($sorted, SORT_NUMERIC);
            $this->listeners[$name] = array_merge(...$sorted);
            $this->sorted[$name] = true;
        }

        return $this->listeners[$name];
    }

    public function on(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self
    {
        $this->listeners[$name][] = new EventHandler($handler, $priority, $extraArgs);
        $this->sorted[$name] = false;

        return $this;
    }

    public function once(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self
    {
        $eventHandler = new EventHandler($handler, $priority, $extraArgs);
        $eventHandler->once = true;
        $this->listeners[$name][] = $eventHandler;
        $this->sorted[$name] = false;

        return $this;
    }

    public function off(string $event, callable $handler = null, int $priority = null): self
    {
        if ($handler === null) {
            unset($this->listeners[$event], $this->sorted[$event]);
            return $this;
        }

        foreach ($this->listeners[$event] as $i => $listener) {
            if ($listener->isSame($handler, $priority)) {
                unset($this->listeners[$event][$i]);
            }
        }

        if (count($this->listeners[$event]) === 0) {
            unset($this->listeners[$event]);
        }

        return $this;
    }
}
