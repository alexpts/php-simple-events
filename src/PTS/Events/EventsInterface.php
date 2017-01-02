<?php
namespace PTS\Events;

interface EventsInterface
{
    public function emit(string $name, array $arguments = []);

    public function on(string $name, callable $handler, int $priority = 50, array $extraArguments = []);

    public function once(string $name, callable $handler, int $priority = 50, array $extraArguments = []);

    public function off(string $eventName, callable $handler = null, int $priority = null);

}
