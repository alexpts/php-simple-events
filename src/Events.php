<?php
declare(strict_types=1);

namespace PTS\Events;

class Events extends BaseEvents implements EventsInterface
{
    /**
     * @param string $name
     * @param array $arguments
     *
     * @return $this
     */
    public function emit(string $name, array $arguments = [])
    {
        try {
            $this->trigger($name, $arguments);
        } catch (StopPropagation $e) {
            return $this;
        }

        return $this;
    }
}
