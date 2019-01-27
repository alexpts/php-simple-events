<?php

namespace PTS\Events;

class Filters extends BaseEvents implements FiltersInterface
{
    /**
     * @param string $name
     * @param mixed $value
     * @param array $arguments
     *
     * @return mixed
     */
    public function filter(string $name, $value, array $arguments = [])
    {
        try {
            return $this->trigger($name, $arguments, $value);
        } catch (StopPropagation $e) {
            return $e->getValue();
        }
    }
}
