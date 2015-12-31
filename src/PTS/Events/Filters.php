<?php
namespace PTS\Events;

class Filters extends BaseEvents
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
        if (isset($this->listeners[$name])) {
            krsort($this->listeners[$name], SORT_NUMERIC);
            foreach ($this->listeners[$name] as $handlers) {
                foreach ($handlers as $paramsHandler) {
                    $callArguments = $this->getCallArgs($arguments, $paramsHandler['extraArguments'], $value);
                    $value = call_user_func_array($paramsHandler['handler'], $callArguments);
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