<?php
declare(strict_types=1);

namespace PTS\Events;

abstract class BaseEvents
{
    /** @var array[][] */
    protected array $listeners = [];
    /** @var bool[] */
    protected array $sorted = [];

    public function getListeners(): array
    {
        return $this->listeners;
    }

    protected function sortListeners(string $name): void
    {
        $isSorted = $this->sorted[$name] ?? false;
        if (!$isSorted) {
            krsort($this->listeners[$name], SORT_NUMERIC);
            $this->sorted[$name] = true;
        }
    }

    public function on(string $name, callable $handler, int $priority = 50, array $extraArguments = []): self
    {
        $handlerId = $this->getHandlerId($handler);
        $this->listeners[$name][$priority][$handlerId] = [
            'handler' => $handler,
            'extraArguments' => $extraArguments,
            'priority' => $priority,
        ];

        $this->sorted[$name] = false;

        return $this;
    }

    public function once(string $name, callable $handler, int $priority = 50, array $extraArguments = []): self
    {
        $this->on($name, $handler, $priority, $extraArguments);
        $handlerId = $this->getHandlerId($handler);
        $this->listeners[$name][$priority][$handlerId]['once'] = true;

        return $this;
    }

    protected function offAll(string $eventName): self
    {
        $hasEvent = $this->listeners[$eventName] ?? false;
        if ($hasEvent) {
            unset($this->listeners[$eventName], $this->sorted[$eventName]);
        }

        return $this;
    }

    public function off(string $eventName, callable $handler = null, int $priority = null): self
    {
        if ($handler === null) {
            return $this->offAll($eventName);
        }

        $priority === null
            ? $this->offHandlerWithoutPriority($eventName, $handler)
            : $this->offHandlerWithPriority($eventName, $handler, $priority);

        if (empty($this->listeners[$eventName])) {
            unset($this->listeners[$eventName], $this->sorted[$eventName]);
        }

        return $this;
    }

    protected function offHandlerWithPriority(string $eventName, callable $handler, int $priority = 50): self
    {
        $handlerId = $this->getHandlerId($handler);

        unset($this->listeners[$eventName][$priority][$handlerId]);
        $this->cleanEmptyEvent($eventName, $priority);

        return $this;
    }

    protected function offHandlerWithoutPriority(string $eventName, callable $handler): self
    {
        $handlerId = $this->getHandlerId($handler);

        foreach ($this->listeners[$eventName] as $currentPriority => $handlers) {
            foreach ($handlers as $currentHandlerId => $paramsHandler) {
                if ($handlerId === $currentHandlerId) {
                    unset($this->listeners[$eventName][$currentPriority][$handlerId]);
                    $this->cleanEmptyEvent($eventName, $currentPriority);
                }
            }
        }

        return $this;
    }

    protected function cleanEmptyEvent(string $eventName, int $currentPriority): void
    {
        if (empty($this->listeners[$eventName][$currentPriority])) {
            unset($this->listeners[$eventName][$currentPriority]);
        }

        if (empty($this->sorted[$eventName])) {
            unset($this->sorted[$eventName]);
        }
    }

    protected function offOnce(array $paramsHandler, string $name): void
    {
        if ($paramsHandler['once'] ?? false) {
            $this->offHandlerWithPriority($name, $paramsHandler['handler'], $paramsHandler['priority']);
        }
    }

    public function getHandlerId(callable $handler): string
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
}
