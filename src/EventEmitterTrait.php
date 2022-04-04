<?php
declare(strict_types=1);

namespace PTS\Events;

trait EventEmitterTrait
{
    /** @var EventHandler[][] */
    protected array $listeners = [];

    public function emitArgs(string $name, array $args = []): void
    {
        try {
            foreach ($this->listeners[$name] ?? [] as $i => $listener) {
                ($listener->handler)(...$args, ...$listener->extraArgs);

                if ($listener->once) {
                    unset($this->listeners[$name][$i]);
                    if (count($this->listeners[$name]) === 0) {
                        unset($this->listeners[$name]);
                    }
                }
            }
        } catch (StopPropagation) {
            return;
        }
    }

    public function emit(string $name, array $args = []): void
    {
        $countArgs = count($args);

        try {
            foreach ($this->listeners[$name] ?? [] as $i => $listener) {
                match ($countArgs) {
                    0 => ($listener->handler)(...$listener->extraArgs),
                    1 => ($listener->handler)($args[0], ...$listener->extraArgs),
                    2 => ($listener->handler)($args[0], $args[1], ...$listener->extraArgs),
                    3 => ($listener->handler)($args[0], $args[1], $args[2], ...$listener->extraArgs),
                    default => ($listener->handler)(...$args, ...$listener->extraArgs),
                };

                if ($listener->once) {
                    unset($this->listeners[$name][$i]);
                    if (count($this->listeners[$name]) === 0) {
                        unset($this->listeners[$name]);
                    }
                }
            }
        } catch (StopPropagation) {
            return;
        }
    }

    public function emitNoArgs(string $name): void
    {
        try {
            foreach ($this->listeners[$name] ?? [] as $i => $listener) {
                ($listener->handler)();

                if ($listener->once) {
                    unset($this->listeners[$name][$i]);
                    if (count($this->listeners[$name]) === 0) {
                        unset($this->listeners[$name]);
                    }
                }
            }
        } catch (StopPropagation) {
            return;
        }
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

    protected function sortEventHandler(string $name): void
    {
        $sorted = [];
        foreach ($this->listeners[$name] as $listener) {
            $sorted[$listener->priority][] = $listener;
        }

        krsort($sorted, SORT_NUMERIC);
        $this->listeners[$name] = array_merge(...$sorted);
    }

    public function on(string $name, callable $handler, int $priority = 50, array $extraArgs = []): static
    {
        $this->listeners[$name][] = new EventHandler($handler, $priority, $extraArgs);
        $this->sortEventHandler($name);

        return $this;
    }

    public function once(string $name, callable $handler, int $priority = 50, array $extraArgs = []): static
    {
        $eventHandler = new EventHandler($handler, $priority, $extraArgs);
        $eventHandler->once = true;
        $this->listeners[$name][] = $eventHandler;

        $this->sortEventHandler($name);

        return $this;
    }

    public function off(string $event, callable $handler = null, int $priority = null): static
    {
        if ($handler === null) {
            unset($this->listeners[$event]);
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
