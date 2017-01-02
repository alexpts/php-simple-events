<?php
namespace PTS\Events;

class Handler
{

    public function getKey(callable $handler) : string
    {
        if (is_array($handler)) {
            list($className, $method) = $handler;

            if (is_object($className)) {
                $className = get_class($className);
            }

            return "{$className}::{$method}";
        }

        return is_string($handler) ? $handler : spl_object_hash($handler);
    }
}
