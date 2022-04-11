<?php
declare(strict_types=1);

namespace PTS\Events\Filter;

use PTS\Events\EventEmitterTrait;
use PTS\Events\StopPropagation;

trait FilterEmitterExtraArgsTrait
{
    use FilterEmitterTrait;

    public function emit(string $name, mixed $value, array $args = []): mixed
    {
        $countArgs = count($args);

        try {
            foreach ($this->listeners[$name] ?? [] as $i => $listener) {
                $value = match ($countArgs) {
                    0 => ($listener->handler)($value, ...$listener->extraArgs),
                    1 => ($listener->handler)($value, $args[0], ...$listener->extraArgs),
                    2 => ($listener->handler)($value, $args[0], $args[1], ...$listener->extraArgs),
                    3 => ($listener->handler)($value, $args[0], $args[1], $args[2], ...$listener->extraArgs),
                    default => ($listener->handler)($value, ...$args, ...$listener->extraArgs),
                };

                if ($listener->once) {
                    unset($this->listeners[$name][$i]);
                    if (count($this->listeners[$name]) === 0) {
                        unset($this->listeners[$name]);
                    }
                }
            }
        } catch (StopPropagation $e) {
            return $e->getValue();
        }

        return $value;
    }

    public function emitNoArgs(string $name, mixed $value): mixed
    {
        try {
            foreach ($this->listeners[$name] ?? [] as $i => $listener) {
                $value = ($listener->handler)($value, ...$listener->extraArgs);

                if ($listener->once) {
                    unset($this->listeners[$name][$i]);
                    if (count($this->listeners[$name]) === 0) {
                        unset($this->listeners[$name]);
                    }
                }
            }
        } catch (StopPropagation $e) {
            return $e->getValue();
        }

        return $value;
    }
}
