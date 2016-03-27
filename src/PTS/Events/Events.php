<?php
namespace PTS\Events;

class Events extends BaseEvents implements EventsInterface
{
    /**
     * @param string $name
     * @param array $arguments
     *
     * @return $this
     */
    public function emit($name, array $arguments = [])
    {
        try {
            $this->trigger($name, $arguments);
        } catch (StopPropagation $e) {}

        return $this;
    }

    /**
     * @param array $arguments
     * @param array $extraArguments
     * @param null $value
     * @return array
     */
    protected function getCallArgs(array $arguments, array $extraArguments, $value = null)
    {
        return array_merge($arguments, $extraArguments);
    }
}
