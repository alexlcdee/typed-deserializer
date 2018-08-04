<?php

namespace AlexLcDee\TypedDeserializer\TypeSystem;


class NotNull extends WrapperType
{
    private $value;
    /**
     * @var Type
     */
    private $wrappedType;

    public function __construct(Type $wrappedType)
    {
        $this->wrappedType = $wrappedType;
    }

    public function deserialize($value)
    {
        if (is_null($value)) {
            throw new \InvalidArgumentException('Value must not be null');
        }
        $object = clone $this->wrappedType;
        $object->deserialize($value);
        $this->value = $object;

        return $this;
    }

    public function getWrappedValue()
    {
        if ($this->value instanceof ScalarType) {
            return $this->value->value();
        }
        if ($this->value instanceof WrapperType) {
            return $this->value->getWrappedValue();
        }

        return $this->value;
    }
}