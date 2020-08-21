<?php
declare(strict_types=1);

namespace PTS\Events\Filter;

use PTS\Events\EventEmitterTrait;
use PTS\Events\StopPropagation;

trait FilterEmitterTrait
{
    use EventEmitterTrait;

    public function emit(string $name, $value, array $args = [])
    {
        $filter = $this->listeners[$name] ?? null;
        if ($filter === null) {
            return $value;
        }

        try {
            foreach ($this->getSortedListeners($name) as $i => $listener) {
                $handler = $listener->handler;
                $value = $handler($value, ...$args, ...$listener->extraArgs);
                if ($listener->once) {
                    unset($this->listeners[$name][$i]);
                }
            }
        } catch (StopPropagation $e) {
            return $e->getValue();
        }

        return $value;
    }
}
