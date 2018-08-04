<?php

namespace AlexLcDee\TypedDeserializer\Tests\TypeSystem;


use AlexLcDee\TypedDeserializer\TypeSystem\Field;
use AlexLcDee\TypedDeserializer\TypeSystem\IntType;
use AlexLcDee\TypedDeserializer\TypeSystem\StringType;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    public function test_field_has_name()
    {
        $name = 'id';
        $field = new Field($name, new IntType());

        $this->assertSame($name, $field->getName());
    }

    public function test_field_has_type()
    {
        $type = new IntType();
        $field = new Field('id', $type);

        $this->assertSame($type, $field->getType());
    }

    public function test_field_can_decode_value()
    {
        $field = new Field('id', new IntType());

        $this->assertEquals((new IntType())->deserialize(1), $field->decode(1));
    }

    public function test_field_can_decode_value_with_callable_decoder()
    {
        $field = new Field('id', new IntType(), null, function (string $value) {
            return (int)$value;
        });

        $this->assertEquals((new IntType())->deserialize(1), $field->decode("1"));
    }

    public function test_field_can_encode_value()
    {
        $field = new Field('id', new IntType());

        $this->assertEquals(1, $field->encode((new IntType())->deserialize(1)));
    }

    public function test_field_can_encode_value_with_callable_encoder()
    {
        $field = new Field('id', new StringType(), function (StringType $value) {
            return (new \DateTimeImmutable($value->value()))->format(\DateTimeInterface::ATOM);
        });

        $now = new \DateTimeImmutable();

        $this->assertEquals(
            $now->format(\DateTimeInterface::ATOM),
            $field->encode((new StringType())->deserialize($now->format('Y-m-d H:i:s')))
        );
    }
}