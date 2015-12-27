<?php
namespace PTS\Events;

class Filters extends Base
{
    /**
     * @param string $name
     * @param mixed $value
     * @param array $arguments
     *
     * @return mixed
     */
    public function emit($name, $value, array $arguments = [])
    {
        if (!isset($this->list[$name])) {
            return $value;
        }

        krsort($this->list[$name], SORT_NUMERIC);
        foreach ($this->list[$name] as $priority => $handlers) {
            foreach ($handlers as $nameHandler => $paramsHandler) {
                $extraArguments = $paramsHandler['extraArguments'];
                $handler = $paramsHandler['handler'];

                if ($extraArguments) {
                    $arguments = array_merge($arguments, $extraArguments);
                }

                array_unshift($arguments, $value);

                $value = call_user_func_array($handler, $arguments);
            }
        }

        return $value;
    }
}