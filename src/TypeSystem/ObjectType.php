<?php

namespace AlexLcDee\TypedDeserializer\TypeSystem;


abstract class ObjectType extends Type
{
    private $values = null;
    private $fieldNames = [];
    private $resolvedFields = [];

    public function __get($name)
    {
        if (empty($this->fieldNames)) {
            $this->fieldNames = array_map(function (Field $field) {
                return $field->getName();
            }, $this->resolveFields());
        }

        if (in_array($name, $this->fieldNames)) {
            return $this->returnValue($this->values[$name] ?? null);
        }

        throw new \TypeError('Property ' . $name . ' not found in type ' . static::class);
    }

    private function returnValue(Type $value = null)
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof ScalarType) {
            return $value->value();
        }
        if ($value instanceof WrapperType) {
            return $value->getWrappedValue();
        }

        return $value;
    }

    abstract public function fields(): array;

    public function deserialize($values)
    {
        if (!is_null($values) && !is_array($values)) {
            throw new \InvalidArgumentException('Values must be array');
        }
        if (!is_null($values)) {
            foreach ($this->resolveFields() as $field) {
                $this->values[$field->getName()] = $this->decodeField($field, $values);
            }
        }

        return $this;
    }

    private function decodeField(Field $field, array $values)
    {
        return $field->decode($values[$field->getName()] ?? null);
    }

    public function jsonSerialize()
    {
        if (is_null($this->values)) {
            return null;
        }
        $result = [];
        foreach ($this->resolveFields() as $field) {
            $result[$field->getName()] = $this->encodeField($field);
        }

        return $result;
    }

    private function encodeField(Field $field)
    {
        return $field->encode($this->values[$field->getName()] ?? null);
    }

    /**
     * @return Field[]
     */
    private function resolveFields()
    {
        if (empty($this->resolvedFields)) {
            foreach ($this->fields() as $key => $value) {
                if ($value instanceof Field) {
                    $this->resolvedFields[] = $value;
                } elseif ($value instanceof Type && is_string($key)) {
                    $this->resolvedFields[] = new Field($key, $value);
                }
            }
        }

        return $this->resolvedFields;
    }
}