<?php
namespace PTS\Events;

class Events extends Base
{
    /**
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function emit($name, array $arguments = [])
    {
        $result = null;

        if (!isset($this->list[$name])){
            return $result;
        }

        krsort($this->list[$name], SORT_NUMERIC);
        foreach ($this->list[$name] as $priority => $handlers) {
            foreach ($handlers as $nameHandler => $paramsHandler) {
                $extraArguments = $paramsHandler['extraArguments'];
                $handler = $paramsHandler['handler'];

                if ($extraArguments) {
                    $arguments = array_merge($arguments, $extraArguments);
                }

                $result = call_user_func_array($handler, $arguments);
            }
        }

        return $result;
    }
}