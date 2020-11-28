<?php
declare(strict_types=1);

namespace PTS\Events;

use Exception;

class StopPropagation extends Exception
{
    /** @var mixed */
    public $value;

    public function setValue(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }
}
