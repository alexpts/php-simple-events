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
    public function filter($name, $value, array $arguments = [])
    {
        return $this->trigger($name, $arguments, $value);
    }
}
