<?php
namespace PTS\Events;

class StopPropagation extends \Exception
{
    /** @var mixed */
    public $value;

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
