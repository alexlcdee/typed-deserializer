<?php

namespace AlexLcDee\TypedDeserializer\Tests\TypeSystem;


use AlexLcDee\TypedDeserializer\TypeSystem\Field;
use AlexLcDee\TypedDeserializer\TypeSystem\IntType;
use AlexLcDee\TypedDeserializer\TypeSystem\ListOf;
use AlexLcDee\TypedDeserializer\TypeSystem\NotNull;
use AlexLcDee\TypedDeserializer\TypeSystem\ObjectType;
use PHPUnit\Framework\TestCase;

class NotNullTest extends TestCase
{
    public function test_not_null_throws_exception_on_null_field()
    {
        $object = new class extends ObjectType
        {
            /**
             * @return Field[]
             */
            public function fields(): array
            {
                return [
                    new Field('not_null_object', new NotNull(new class extends ObjectType
                    {
                        /**
                         * @return Field[]
                         */
                        public function fields(): array
                        {
                            return [
                                new Field('id', new IntType()),
                            ];
                        }
                    })),
                ];
            }
        };

        $this->expectException(\InvalidArgumentException::class);
        $object->deserialize([]);
    }

    public function test_not_null_can_return_scalar()
    {
        $object = new class extends ObjectType
        {
            /**
             * @return Field[]
             */
            public function fields(): array
            {
                return [
                    new Field('not_null', new NotNull(new IntType())),
                ];
            }
        };

        $object->deserialize([
            'not_null' => 1,
        ]);

        $this->assertSame(1, $object->not_null);
    }

    public function test_not_null_can_return_nested_value()
    {
        $object = new class extends ObjectType
        {
            /**
             * @return Field[]
             */
            public function fields(): array
            {
                return [
                    new Field('not_null_object', new NotNull(new class extends ObjectType
                    {
                        /**
                         * @return Field[]
                         */
                        public function fields(): array
                        {
                            return [
                                new Field('id', new IntType()),
                            ];
                        }
                    })),
                ];
            }
        };

        $object->deserialize([
            'not_null_object' => [
                'id' => 123,
            ],
        ]);

        $this->assertSame(123, $object->not_null_object->id);
    }

    public function test_not_null_can_wrapp_other_wrappers()
    {
        $object = new class extends ObjectType
        {
            /**
             * @return Field[]
             */
            public function fields(): array
            {
                return [
                    new Field('ints', new NotNull(new ListOf(new IntType()))),
                ];
            }
        };

        $object->deserialize([
            'ints' => [1],
        ]);

        $this->assertSame([1], $object->ints);
    }
}