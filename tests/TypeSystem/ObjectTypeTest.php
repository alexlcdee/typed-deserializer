<?php

namespace AlexLcDee\TypedDeserializer\Tests\TypeSystem;


use AlexLcDee\TypedDeserializer\TypeSystem\Field;
use AlexLcDee\TypedDeserializer\TypeSystem\IntType;
use AlexLcDee\TypedDeserializer\TypeSystem\NotNull;
use AlexLcDee\TypedDeserializer\TypeSystem\ObjectType;
use AlexLcDee\TypedDeserializer\TypeSystem\StringType;
use PHPUnit\Framework\TestCase;

class ObjectTypeTest extends TestCase
{
    public function test_can_deserialize_object()
    {
        $object = new class extends ObjectType
        {
            /**
             * @return Field[]
             */
            public function fields(): array
            {
                return [
                    new Field('id', new IntType()),
                    new Field('name', new StringType()),
                    new Field('i_am_null', new StringType()),
                ];
            }
        };

        $object->deserialize(['id' => 1, 'name' => 'test string']);

        $this->assertSame(1, $object->id);
        $this->assertSame('test string', $object->name);
        $this->assertNull($object->i_am_null);
    }

    public function test_can_serialize_nested_objects()
    {
        $object = new class extends ObjectType
        {
            /**
             * @return Field[]
             */
            public function fields(): array
            {
                return [
                    new Field('nested_object', new class extends ObjectType
                    {
                        /**
                         * @return Field[]
                         */
                        public function fields(): array
                        {
                            return [
                                new Field('deep_nested_object', new class extends ObjectType
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
                                }),
                            ];
                        }
                    }),
                ];
            }
        };

        $object->deserialize([
            'nested_object' => [
                'deep_nested_object' => [
                    'id' => 123,
                ],
            ],
        ]);

        $this->assertSame(123, $object->nested_object->deep_nested_object->id);
        $this->assertSame([
            'nested_object' => [
                'deep_nested_object' => [
                    'id' => 123,
                ],
            ],
        ], json_decode(json_encode($object->jsonSerialize()), true));
    }

    public function test_object_type_can_use_key_value_pairs_for_field_definitions()
    {
        $object = new class extends ObjectType
        {
            public function fields(): array
            {
                return [
                    'id' => new NotNull(new IntType()),
                ];
            }
        };

        $object->deserialize(['id' => 1]);

        $this->assertSame(1, $object->id);
    }
}