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
        if (array_key_exists($name, $this->listeners)) {
            $this->sortListeners($name);
            foreach ($this->listeners[$name] as $handlers) {
                foreach ($handlers as $paramsHandler) {
                    $arguments = array_merge($arguments, $paramsHandler['extraArguments']);
                    call_user_func_array($paramsHandler['handler'], $arguments);
                }
            }
        }

        return $this;
    }
}
