<?php
declare(strict_types=1);

namespace PTS\Events;

interface EventEmitterInterface
{
    public function emit(string $name, array $args = []): self;
    public function on(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self;
    public function once(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self;
    public function off(string $event, callable $handler = null, int $priority = null): self;
}