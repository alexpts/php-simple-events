<?php
declare(strict_types=1);

namespace PTS\Events;

interface FiltersInterface
{

    public function filter(string $name, $value, array $arguments = []);
    public function on(string $name, callable $handler, int $priority = 50, array $extraArguments = []);
    public function once(string $name, callable $handler, int $priority = 50, array $extraArguments = []);
    public function off(string $eventName, callable $handler = null, int $priority = null);

}
