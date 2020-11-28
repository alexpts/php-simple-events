<?php
declare(strict_types=1);

namespace PTS\Events;

interface EventEmitterInterface
{
    public function emit(string $name, array $args = []): void;
    public function on(string $name, callable $handler, int $priority = 50, array $extraArgs = []): static;
    public function once(string $name, callable $handler, int $priority = 50, array $extraArgs = []): static;
    public function off(string $event, callable $handler = null, int $priority = null): static;

    public function listeners(string $event = null): array;
    public function eventNames(): array;
}