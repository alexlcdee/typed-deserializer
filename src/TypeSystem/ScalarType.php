<?php

namespace AlexLcDee\TypedDeserializer\TypeSystem;


abstract class ScalarType extends Type
{
    private $value;

    public function deserialize($value)
    {
        if (!is_null($value) && !is_scalar($value)) {
            throw new \InvalidArgumentException('Value must be scalar');
        }

        $this->value = $value;

        return $this;
    }

    public function value()
    {
        return $this->value;
    }

    public function jsonSerialize()
    {
        return $this->value;
    }
}