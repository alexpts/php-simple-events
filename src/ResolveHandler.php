<?php
declare(strict_types=1);

namespace PTS\Events;

class ResolveHandler
{

    public function getKey(callable $handler): string
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
