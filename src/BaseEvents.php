<?php
declare(strict_types=1);

namespace PTS\Events;

abstract class BaseEvents
{
    /** @var array[][] */
    protected $listeners = [];
    /** @var ResolveHandler */
    protected $handler;
    /** @var bool */
    protected $sorted = false;

    public function __construct(ResolveHandler $handler = null)
    {
        $this->handler = $handler ?? new ResolveHandler;
    }

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

    /**
     * @param string $name
     * @param callable $handler
     * @param int $priority
     * @param array $extraArguments
     *
     * @return $this
     */
    public function on(string $name, callable $handler, int $priority = 50, array $extraArguments = [])
    {
        $handlerId = $this->handler->getKey($handler);
        $this->listeners[$name][$priority][$handlerId] = [
            'handler' => $handler,
            'extraArguments' => $extraArguments,
            'priority' => $priority,
        ];

        $this->sorted[$name] = false;

        return $this;
    }

    /**
     * @param string $name
     * @param callable $handler
     * @param int $priority
     * @param array $extraArguments
     *
     * @return $this
     */
    public function once(string $name, callable $handler, int $priority = 50, array $extraArguments = []): self
    {
        $this->on($name, $handler, $priority, $extraArguments);
        $handlerId = $this->handler->getKey($handler);
        $this->listeners[$name][$priority][$handlerId]['once'] = true;

        return $this;
    }

    /**
     * @param string $eventName
     *
     * @return $this
     */
    protected function offAll(string $eventName): self
    {
        $hasEvent = $this->listeners[$eventName] ?? false;
        if ($hasEvent) {
            unset($this->listeners[$eventName], $this->sorted[$eventName]);
        }

        return $this;
    }

    /**
     * @param string $eventName
     * @param callable|null $handler
     * @param int|null $priority
     *
     * @return $this
     */
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

    /**
     * @param string $eventName
     * @param callable $handler
     * @param int $priority
     *
     * @return $this
     */
    protected function offHandlerWithPriority(string $eventName, callable $handler, int $priority = 50): self
    {
        $handlerId = $this->handler->getKey($handler);

        if (isset($this->listeners[$eventName][$priority][$handlerId])) {
            unset($this->listeners[$eventName][$priority][$handlerId]);
            $this->cleanEmptyEvent($eventName, $priority);
        }

        return $this;
    }

    /**
     * @param string $eventName
     * @param callable $handler
     *
     * @return $this
     */
    protected function offHandlerWithoutPriority(string $eventName, callable $handler): self
    {
        $handlerId = $this->handler->getKey($handler);

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

    /**
     * @param string $name
     * @param array $arguments
     * @param mixed $value
     *
     * @return mixed
     */
    protected function trigger(string $name, array $arguments = [], $value = null)
    {
        $event = $this->listeners[$name] ?? false;
        if ($event) {
            $this->sortListeners($name);
            foreach ($this->listeners[$name] as $handlers) {
                foreach ($handlers as $paramsHandler) {
                    $callArgs = $this->getCallArgs($arguments, $paramsHandler['extraArguments'], $value);
                    $value = $paramsHandler['handler'](...$callArgs);
                    $this->offOnce($paramsHandler, $name);
                }
            }
        }

        return $value;
    }

    /**
     * @param array $paramsHandler
     * @param string $name
     */
    protected function offOnce(array $paramsHandler, string $name): void
    {
        if ($paramsHandler['once'] ?? false) {
            $this->off($name, $paramsHandler['handler'], $paramsHandler['priority']);
        }
    }

    /**
     * @param array $arguments
     * @param array $extraArguments
     * @param mixed $value
     *
     * @return array
     */
    protected function getCallArgs(array $arguments, array $extraArguments, $value = null): array
    {
        foreach ($extraArguments as $name => $val) {
            $arguments[] = $val;
        }

        return $arguments;
    }
}
