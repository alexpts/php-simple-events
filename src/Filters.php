<?php
declare(strict_types=1);

namespace PTS\Events;

class Filters extends BaseEvents implements FiltersInterface
{

    public function filter(string $name, $value, array $arguments = [])
    {
        $filter = $this->listeners[$name] ?? false;
        if ($filter === false) {
            return $value;
        }

        try {
            $this->sortListeners($name);
            foreach ($this->listeners[$name] as $handlers) {
                foreach ($handlers as $paramsHandler) {
                    $value = $paramsHandler['handler']($value, ...$arguments, ...$paramsHandler['extraArguments']);
                    $this->offOnce($paramsHandler, $name);
                }
            }
        } catch (StopPropagation $e) {
            return $e->getValue();
        }

        return $value;
    }
}
