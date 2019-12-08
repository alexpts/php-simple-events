<?php
declare(strict_types=1);

namespace PTS\Events;

class Events extends BaseEvents implements EventsInterface
{

    public function emit(string $name, array $arguments = []): self
    {
        $event = $this->listeners[$name] ?? false;
        if (!$event) {
            return $this;
        }

        try {
            $this->sortListeners($name);
            foreach ($this->listeners[$name] as $handlers) {
                foreach ($handlers as $paramsHandler) {
                    $paramsHandler['handler'](...$arguments, ...$paramsHandler['extraArguments']);
                    $this->offOnce($paramsHandler, $name);
                }
            }
        } catch (StopPropagation $e) {
            return $this;
        }

        return $this;
    }
}
