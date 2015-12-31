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
        if (!isset($this->listeners[$name])) {
            return $value;
        }

        krsort($this->listeners[$name], SORT_NUMERIC);
        foreach ($this->listeners[$name] as $handlers) {
            foreach ($handlers as $paramsHandler) {
                $callArguments = $arguments;
                $extraArguments = $paramsHandler['extraArguments'];
                if ($extraArguments) {
                    $callArguments = array_merge($callArguments, $extraArguments);
                }

                array_unshift($callArguments, $value);
                $value = call_user_func_array($paramsHandler['handler'], $callArguments);
            }
        }

        return $value;
    }
}