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
    public function emit(string $name, array $arguments = []): self
    {
        try {
            $this->trigger($name, $arguments);
        } catch (StopPropagation $e) {
            return $this;
        }

        return $this;
    }

    protected function getCallArgs(array $arguments, array $extraArguments, $value = null): array
    {
        return array_merge($arguments, $extraArguments);
    }
}
