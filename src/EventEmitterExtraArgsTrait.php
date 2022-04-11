<?php
declare(strict_types=1);

namespace PTS\Events;

trait EventEmitterExtraArgsTrait
{
    use EventEmitterTrait;

    public function emit(string $name, array $args = []): void
    {
        $countArgs = count($args);

        try {
            foreach ($this->listeners[$name] ?? [] as $i => $listener) {
                match ($countArgs) {
                    0 => ($listener->handler)(...$listener->extraArgs),
                    1 => ($listener->handler)($args[0], ...$listener->extraArgs),
                    2 => ($listener->handler)($args[0], $args[1], ...$listener->extraArgs),
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
                ($listener->handler)(...$listener->extraArgs);

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
}
