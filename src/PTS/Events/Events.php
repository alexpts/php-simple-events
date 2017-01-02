<?php
namespace PTS\Events;

class Events extends BaseEvents implements EventsInterface
{
    public function emit(string $name, array $arguments = [])
    {
        try {
            $this->trigger($name, $arguments);
        } catch (StopPropagation $e) {}

        return $this;
    }

    protected function getCallArgs(array $arguments, array $extraArguments, $value = null) : array
    {
        return array_merge($arguments, $extraArguments);
    }
}
