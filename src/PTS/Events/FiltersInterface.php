<?php
namespace PTS\Events;

interface FiltersInterface
{
    /**
     * @param string $name
     * @param mixed $value
     * @param array $arguments
     * @return mixed $value
     */
    public function filter($name, $value, array $arguments = []);

    /**
     * @param string $name
     * @param callable $handler
     * @param int $priority
     * @param array $extraArguments
     * @return FiltersInterface
     */
    public function on($name, callable $handler, $priority = 50, array $extraArguments = []);

    /**
     * @param string $name
     * @param callable $handler
     * @param int $priority
     * @param array $extraArguments
     * @return FiltersInterface
     */
    public function once($name, callable $handler, $priority = 50, array $extraArguments = []);

    /**
     * @param string $eventName
     * @param callable|null $handler
     * @param null|int $priority
     * @return FiltersInterface
     */
    public function off($eventName, callable $handler = null, $priority = null);

}
