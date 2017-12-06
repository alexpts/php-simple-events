<?php

namespace PTS\Events;

interface FiltersInterface
{
    /**
     * @param string $name
     * @param mixed $value
     * @param array $arguments
     *
     * @return $this
     */
    public function filter(string $name, $value, array $arguments = []);

    /**
     * @param string $name
     * @param callable $handler
     * @param int $priority
     * @param array $extraArguments
     *
     * @return $this
     */
    public function on(string $name, callable $handler, int $priority = 50, array $extraArguments = []);

    /**
     * @param string $name
     * @param callable $handler
     * @param int $priority
     * @param array $extraArguments
     *
     * @return $this
     */
    public function once(string $name, callable $handler, int $priority = 50, array $extraArguments = []);

    /**
     * @param string $eventName
     * @param callable|null $handler
     * @param int|null $priority
     *
     * @return $this
     */
    public function off(string $eventName, callable $handler = null, int $priority = null);

}
