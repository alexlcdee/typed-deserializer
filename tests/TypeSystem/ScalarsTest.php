<?php

namespace AlexLcDee\TypedDeserializer\Tests\TypeSystem;


use AlexLcDee\TypedDeserializer\TypeSystem\BoolType;
use AlexLcDee\TypedDeserializer\TypeSystem\FloatType;
use AlexLcDee\TypedDeserializer\TypeSystem\IntType;
use AlexLcDee\TypedDeserializer\TypeSystem\ScalarType;
use AlexLcDee\TypedDeserializer\TypeSystem\StringType;
use PHPUnit\Framework\TestCase;

class ScalarsTest extends TestCase
{
    public function test_can_deserialize_int()
    {
        $object = new IntType();

        $object->deserialize(2);
        $this->assertSame(2, $object->value());
    }

    public function test_cannot_deserialize_string_as_int()
    {
        $object = new IntType();

        $this->expectException(\InvalidArgumentException::class);
        $object->deserialize('2');
    }

    public function test_int_type_allows_null()
    {
        $object = new IntType();

        $object->deserialize(null);
        $this->assertSame(null, $object->value());
    }

    public function test_can_deserialize_float()
    {
        $object = new FloatType();

        $object->deserialize(2.1);
        $this->assertSame(2.1, $object->value());
    }

    public function test_cannot_deserialize_string_as_float()
    {
        $object = new FloatType();

        $this->expectException(\InvalidArgumentException::class);
        $object->deserialize('2.1');
    }

    public function test_float_type_allows_null()
    {
        $object = new FloatType();

        $object->deserialize(null);
        $this->assertSame(null, $object->value());
    }

    public function test_can_deserialize_string()
    {
        $object = new StringType();

        $object->deserialize('string');
        $this->assertSame('string', $object->value());
    }

    public function test_cannot_deserialize_int_as_string()
    {
        $object = new StringType();

        $this->expectException(\InvalidArgumentException::class);
        $object->deserialize(2);
    }

    public function test_string_type_allows_null()
    {
        $object = new StringType();

        $object->deserialize(null);
        $this->assertSame(null, $object->value());
    }

    public function test_can_deserialize_bool()
    {
        $object = new BoolType();

        $object->deserialize(true);
        $this->assertSame(true, $object->value());

        $object->deserialize(false);
        $this->assertSame(false, $object->value());
    }

    public function test_cannot_deserialize_string_as_bool()
    {
        $object = new BoolType();

        $this->expectException(\InvalidArgumentException::class);
        $object->deserialize('true');
    }

    public function test_bool_type_allows_null()
    {
        $object = new BoolType();

        $object->deserialize(null);
        $this->assertSame(null, $object->value());
    }

    public function test_cannot_serialize_nonscalar_value_as_scalar_type()
    {
        $object = new class extends ScalarType
        {
        };

        $this->expectException(\InvalidArgumentException::class);
        $object->deserialize(['test']);
    }
}