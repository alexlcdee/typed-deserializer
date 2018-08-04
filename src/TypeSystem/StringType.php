<?php

namespace AlexLcDee\TypedDeserializer\TypeSystem;


class StringType extends ScalarType
{
    public function deserialize($value)
    {
        if (!is_null($value) && !is_string($value)) {
            throw new \InvalidArgumentException('Value must be type of string');
        }
        return parent::deserialize($value);
    }
}