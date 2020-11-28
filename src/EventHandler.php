<?php
declare(strict_types=1);

namespace PTS\Events;

class EventHandler
{
    public string $id;
    /** @var callable */
    public $handler;
    public bool $once = false;

    public function __construct(
        callable $handler,
        public int $priority = 0,
        public array $extraArgs = []
    ) {
        $this->id = $this->getHandlerId($handler);
        $this->handler = $handler;
    }

    protected function getHandlerId(callable $handler): string
    {
        if (is_array($handler)) {
            [$className, $method] = $handler;

            if (is_object($className)) {
                $className = get_class($className);
            }

            return "{$className}::{$method}";
        }

        return is_string($handler) ? $handler : spl_object_hash($handler);
    }

    public function isSame(callable $handler, int $priority = null): bool
    {
        $withPriority = $priority !== null;
        if ($withPriority && $priority !== $this->priority) {
            return false;
        }

        return $this->id === $this->getHandlerId($handler);
    }
}