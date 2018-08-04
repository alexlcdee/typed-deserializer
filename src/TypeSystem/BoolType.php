<?php

namespace AlexLcDee\TypedDeserializer\TypeSystem;


class BoolType extends ScalarType
{
    public function deserialize($value)
    {
        if (!is_null($value) && !is_bool($value)) {
            throw new \InvalidArgumentException('Value must be boolean');
        }
        return parent::deserialize($value);
    }
}