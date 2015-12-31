<?php
namespace PTS\Events;

abstract class BaseEvents
{
    /** @var array */
    protected $listeners = [];

    /**
     * @return array
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * @param string $name
     * @param callable $handler
     * @param int $priority
     * @param array $extraArguments
     * @return FiltersInterface
     */
    public function on($name, callable $handler, $priority = 50, array $extraArguments = [])
    {
        $handlerId = $this->createHandlerId($handler);
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
        if (isset($this->listeners[$eventName])) {
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
        $handlerId = $this->createHandlerId($handler);

        if (isset($this->listeners[$eventName][$priority][$handlerId])) {
            unset($this->listeners[$eventName][$priority][$handlerId]);

            if (empty($this->listeners[$eventName][$priority])) {
                unset($this->listeners[$eventName][$priority]);
            }
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
        $handlerId = $this->createHandlerId($handler);

        foreach ($this->listeners[$eventName] as $currentPriority => $nandlers) {
            foreach ($nandlers as $currentHandlerId => $paramsHandler) {
                if ($handlerId === $currentHandlerId){
                    unset($this->listeners[$eventName][$currentPriority][$handlerId]);

                    if (empty($this->listeners[$eventName][$currentPriority])) {
                        unset($this->listeners[$eventName][$currentPriority]);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param callable $handler
     * @return string
     */
    protected function createHandlerId(callable $handler)
    {
        if (is_array($handler)) {
            list($className, $method) = $handler;
            if (is_object($className)) {
                $className = get_class($className);
            }

            return $className . '::' . $method;
        }

        return is_string($handler) ? $handler : spl_object_hash($handler);
    }
}