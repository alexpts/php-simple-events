<?php
declare(strict_types=1);

namespace PTS\Events;

interface EventsInterface
{

    public function emit(string $name, array $arguments = []);
    public function on(string $name, callable $handler, int $priority = 50, array $extraArgs = []);
    public function once(string $name, callable $handler, int $priority = 50, array $extraArgs = []);
    public function off(string $eventName, callable $handler = null, int $priority = null);

}
