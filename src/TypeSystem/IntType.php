<?php

namespace AlexLcDee\TypedDeserializer\TypeSystem;


class IntType extends ScalarType
{
    public function deserialize($value)
    {
        if (!is_null($value) && !is_int($value)) {
            throw new \InvalidArgumentException('Value must be type of int');
        }
        return parent::deserialize($value);
    }
}