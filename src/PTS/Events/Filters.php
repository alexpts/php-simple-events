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
        if (array_key_exists($name, $this->listeners)) {
            $this->sortListeners($name);
            foreach ($this->listeners[$name] as $handlers) {
                foreach ($handlers as $paramsHandler) {
                    $callArgs = $this->getCallArgs($arguments, $paramsHandler['extraArguments'], $value);
                    $value = call_user_func_array($paramsHandler['handler'], $callArgs);
                }
            }
        }

        return $value;
    }

    /**
     * @param array $arguments
     * @param array $extraArguments
     * @param mixed $value
     * @return array
     */
    protected function getCallArgs(array $arguments, array $extraArguments, $value)
    {
        $arguments = array_merge($arguments, $extraArguments);
        array_unshift($arguments, $value);
        return $arguments;
    }
}
