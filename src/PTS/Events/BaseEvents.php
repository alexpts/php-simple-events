<?php
namespace PTS\Events;

abstract class BaseEvents
{
    /** @var array */
    protected $listeners = [];
    /** @var Handler */
    protected $handler;

    public function __construct()
    {
        $this->handler = new Handler;
    }

    /**
     * @return array
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * @param string $name
     */
    protected function sortListeners($name)
    {
        if (array_key_exists($name, $this->listeners)) {
            krsort($this->listeners[$name], SORT_NUMERIC);
        }
    }

    /**
     * @param string $name
     * @param callable $handler
     * @param int $priority
     * @param array $extraArguments
     * @return $this
     */
    public function on($name, callable $handler, $priority = 50, array $extraArguments = [])
    {
        $handlerId = $this->handler->getKey($handler);
        $this->listeners[$name][$priority][$handlerId] = [
            'handler' => $handler,
            'extraArguments' => $extraArguments,
        ];

        return $this;
    }

    /**
     * @param string $eventName
     * @return $this
     */
    protected function offAll($eventName)
    {
        if (array_key_exists($eventName, $this->listeners)) {
            unset($this->listeners[$eventName]);
        }

        return $this;
    }

    /**
     * @param string $eventName
     * @param callable|null $handler
     * @param null|int $priority
     * @return $this
     */
    public function off($eventName, callable $handler = null, $priority = null)
    {
        if ($handler === null) {
            return $this->offAll($eventName);
        }

        $priority === null
            ? $this->offHandlerWithoutPriority($eventName, $handler)
            : $this->offHandlerWithPriority($eventName, $handler, $priority);

        if (empty($this->listeners[$eventName])) {
            unset($this->listeners[$eventName]);
        }

        return $this;
    }

    /**
     * @param string $eventName
     * @param callable $handler
     * @param int $priority
     * @return $this
     */
    protected function offHandlerWithPriority($eventName, callable $handler, $priority = 50)
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
     * @return $this
     */
    protected function offHandlerWithoutPriority($eventName, callable $handler)
    {
        $handlerId = $this->handler->getKey($handler);

        foreach ($this->listeners[$eventName] as $currentPriority => $nandlers) {
            foreach ($nandlers as $currentHandlerId => $paramsHandler) {
                if ($handlerId === $currentHandlerId) {
                    unset($this->listeners[$eventName][$currentPriority][$handlerId]);
                    $this->cleanEmptyEvent($eventName, $currentPriority);
                }
            }
        }

        return $this;
    }

    /**
     * @param string $eventName
     * @param int $currentPriority
     */
    protected function cleanEmptyEvent($eventName, $currentPriority)
    {
        if (empty($this->listeners[$eventName][$currentPriority])) {
            unset($this->listeners[$eventName][$currentPriority]);
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @param mixed $value
     * @return mixed
     */
    protected function trigger($name, array $arguments = [], $value = null)
    {
        if (array_key_exists($name, $this->listeners)) {
            $this->sortListeners($name);
            foreach ($this->listeners[$name] as $handlers) {
                foreach ($handlers as $paramsHandler) {
                    $callArgs = $this->getCallArgs($arguments, $paramsHandler['extraArguments'], $value);
                    $value = call_user_func_array($paramsHandler['handler'], $callArgs);
                }
            }
        }

        return $value;
    }


    /**
     * @param array $arguments
     * @param array $extraArguments
     * @param null $value
     * @return array
     */
    protected function getCallArgs(array $arguments, array $extraArguments, $value = null)
    {
        $arguments = array_merge($arguments, $extraArguments);
        array_unshift($arguments, $value);
        return $arguments;
    }
}
