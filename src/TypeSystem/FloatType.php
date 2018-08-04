<?php

namespace AlexLcDee\TypedDeserializer\TypeSystem;


class FloatType extends ScalarType
{
    public function deserialize($value)
    {
        if (!is_null($value) && !is_float($value)) {
            throw new \InvalidArgumentException('Value must be float');
        }

        return parent::deserialize($value);
    }
}