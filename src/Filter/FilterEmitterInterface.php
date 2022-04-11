<?php
declare(strict_types=1);

namespace PTS\Events\Filter;

use PTS\Events\EventHandler;

interface FilterEmitterInterface
{
    public function emit(string $name, mixed $value, array $args = []): mixed;
    public function emitNoArgs(string $name, mixed $value): mixed;

    public function on(string $name, callable $handler, int $priority = 50, array $extraArgs = []): static;
    public function once(string $name, callable $handler, int $priority = 50, array $extraArgs = []): static;
    public function off(string $event, callable $handler = null, int $priority = null): static;

    /**
     * @param string|null $event
     *
     * @return EventHandler[][]|EventHandler[]
     */
    public function listeners(string $event = null): array;
    public function eventNames(): array;
}
