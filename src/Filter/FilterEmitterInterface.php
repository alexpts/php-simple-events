<?php
declare(strict_types=1);

namespace PTS\Events\Filter;

use PTS\Events\EventHandler;

interface FilterEmitterInterface
{
    public function emit(string $name, $value, array $args = []);
    public function on(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self;
    public function once(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self;
    public function off(string $event, callable $handler = null, int $priority = null): self;

    /**
     * @param string|null $event
     *
     * @return EventHandler[][]|EventHandler[]
     */
    public function listeners(string $event = null): array;
    public function eventNames(): array;
}
