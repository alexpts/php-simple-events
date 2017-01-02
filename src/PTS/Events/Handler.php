<?php
namespace PTS\Events;

class Handler
{
    /**
     * @param callable $handler
     * @return string
     */
    public function getKey(callable $handler)
    {
        if (is_array($handler)) {
            [$className, $method] = $handler;
            if (is_object($className)) {
                $className = get_class($className);
            }

            return $className . '::' . $method;
        }

        return is_string($handler) ? $handler : spl_object_hash($handler);
    }
}
