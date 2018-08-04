<?php

namespace AlexLcDee\TypedDeserializer\TypeSystem;


use Traversable;

class ListOf extends WrapperType implements \Countable, \IteratorAggregate
{
    /**
     * @var Type
     */
    private $wrappedType;

    private $data = [];
    private $allowEmpty = true;

    public function __construct(Type $wrappedType)
    {
        $this->wrappedType = $wrappedType;
    }

    public function deserialize($value)
    {
        if (!is_null($value) && !is_array($value)) {
            throw new \InvalidArgumentException('Value must be array');
        }
        if (!$this->allowEmpty && empty($value)) {
            throw new \InvalidArgumentException('Value must not be empty');
        }
        if (!is_null($value)) {
            $this->data = array_map(function ($row) {
                $object = (clone $this->wrappedType);
                $object->deserialize($row);

                return $object;
            }, $value);
        }

        return $this;
    }

    public function allowEmpty(bool $allowEmpty)
    {
        $this->allowEmpty = $allowEmpty;

        return $this;
    }

    public function getWrappedValue()
    {
        return array_map(function (Type $object) {
            if ($object instanceof ScalarType) {
                return $object->value();
            }
            if ($object instanceof WrapperType) {
                return $object->getWrappedValue();
            }

            return $object;
        }, $this->data);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getWrappedValue());
    }
}