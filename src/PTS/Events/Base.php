<?php
namespace PTS\Events;

class Base
{
    /** @var array */
    protected $list = [];

    /**
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param string $name
     * @param callable $handler
     * @param int $priority
     * @param array $extraArguments
     */
    public function addHandler($name, callable $handler, $priority = 50, array $extraArguments = [])
    {
        $handlerId = $this->createHandlerId($handler);
        $this->list[$name][$priority][$handlerId] = [
            'handler' => $handler,
            'extraArguments' => $extraArguments,
        ];
    }

    /**
     * @param string $eventName
     */
    public function clearHandlers($eventName)
    {
        if (isset($this->list[$eventName])) {
            unset($this->list[$eventName]);
        }
    }

    /**
     * @param string $eventName
     * @param callable $handler
     * @param null|int $priority
     */
    public function deleteHandler($eventName, callable $handler, $priority = null)
    {
        $handlerId = $this->createHandlerId($handler);

        if (is_int($priority)) {
            if (isset($this->list[$eventName][$priority][$handlerId])) {
                unset($this->list[$eventName][$priority][$handlerId]);

                if (empty($this->list[$eventName][$priority])) {
                    unset($this->list[$eventName][$priority]);
                }
            }
        } else {
            foreach ($this->list[$eventName] as $currentPriority => $nandlers) {
                foreach ($nandlers as $currentHandlerId => $paramsHandler) {
                    if ($handlerId === $currentHandlerId){
                        unset($this->list[$eventName][$currentPriority][$handlerId]);

                        if (empty($this->list[$eventName][$currentPriority])) {
                            unset($this->list[$eventName][$currentPriority]);
                        }
                    }
                }
            }
        }

        if (empty($this->list[$eventName])) {
            unset($this->list[$eventName]);
        }
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