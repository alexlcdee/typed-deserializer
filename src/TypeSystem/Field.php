<?php

namespace AlexLcDee\TypedDeserializer\TypeSystem;


class Field
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var Type
     */
    private $type;
    /**
     * @var callable
     */
    private $encode;
    /**
     * @var callable
     */
    private $decode;

    public function __construct(string $name, Type $type, callable $encode = null, callable $decode = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->encode = $encode;
        $this->decode = $decode;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    public function encode(Type $value = null)
    {
        if ($this->encode) {
            $value = call_user_func($this->encode, $value);
        }

        if ($value instanceof ScalarType) {
            return $value->value();
        }

        if ($value instanceof WrapperType) {
            return $value->getWrappedValue();
        }

        return $value;
    }

    public function decode($value)
    {
        if ($this->decode) {
            $value = call_user_func($this->decode, $value);
        }
        $object = (clone $this->getType());
        $object->deserialize($value);

        return $object;
    }
}