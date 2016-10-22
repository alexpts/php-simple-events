<?php
namespace PTS\Events;

interface EventsInterface
{
    /**
     * @param string $name
     * @param array $arguments
     * @return EventsInterface
     */
    public function emit($name, array $arguments = []);

    /**
     * @param string $name
     * @param callable $handler
     * @param int $priority
     * @param array $extraArguments
     * @return EventsInterface
     */
    public function on($name, callable $handler, $priority = 50, array $extraArguments = []);

    /**
     * @param string $name
     * @param callable $handler
     * @param int $priority
     * @param array $extraArguments
     * @return EventsInterface
     */
    public function once($name, callable $handler, $priority = 50, array $extraArguments = []);

    /**
     * @param string $eventName
     * @param callable|null $handler
     * @param null|int $priority
     * @return EventsInterface
     */
    public function off($eventName, callable $handler = null, $priority = null);

}
