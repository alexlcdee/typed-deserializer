<?php

namespace AlexLcDee\TypedDeserializer\Tests\TypeSystem;


use AlexLcDee\TypedDeserializer\TypeSystem\Field;
use AlexLcDee\TypedDeserializer\TypeSystem\IntType;
use AlexLcDee\TypedDeserializer\TypeSystem\ListOf;
use AlexLcDee\TypedDeserializer\TypeSystem\NotNull;
use AlexLcDee\TypedDeserializer\TypeSystem\ObjectType;
use PHPUnit\Framework\TestCase;

class ListOfTest extends TestCase
{
    public function test_can_deserialize_list_of_scalars()
    {
        $object = new class extends ObjectType
        {
            /**
             * @return Field[]
             */
            public function fields(): array
            {
                return [
                    'ints' => new ListOf(new IntType()),
                ];
            }
        };

        $object->deserialize([
            'ints' => [1, 2, 3, 4],
        ]);

        $this->assertSame([
            'ints' => [1, 2, 3, 4],
        ], json_decode(json_encode($object), true));
    }

    public function test_cannot_deserialize_mixed_list()
    {
        $object = new class extends ObjectType
        {
            /**
             * @return Field[]
             */
            public function fields(): array
            {
                return [
                    'ints' => new ListOf(new IntType()),
                ];
            }
        };

        $this->expectException(\InvalidArgumentException::class);

        $object->deserialize([
            'ints' => [1, 2, '3', 4],
        ]);
    }

    public function test_cannot_deserialize_with_not_null_and_null_value()
    {
        $object = new class extends ObjectType
        {
            /**
             * @return Field[]
             */
            public function fields(): array
            {
                return [
                    'ints' => new ListOf(new NotNull(new IntType())),
                ];
            }
        };

        $this->expectException(\InvalidArgumentException::class);

        $object->deserialize([
            'ints' => [1, 2, null, 3],
        ]);
    }

    public function test_can_deserialize_with_not_null()
    {
        $object = new class extends ObjectType
        {
            /**
             * @return Field[]
             */
            public function fields(): array
            {
                return [
                    new Field('ints', new ListOf(new NotNull(new IntType()))),
                ];
            }
        };

        $object->deserialize([
            'ints' => [1, 2, 4, 3],
        ]);

        $this->assertSame([1, 2, 4, 3], $object->ints);
    }

    public function test_can_deserialize_list_of_objects()
    {
        $list = new ListOf(new class extends ObjectType
        {
            public function fields(): array
            {
                return [
                    'id' => new IntType(),
                ];
            }
        });

        $values = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ];

        $list->deserialize($values);

        foreach ($list as $k => $value) {
            $this->assertSame($values[$k]['id'], $value->id);
        }
    }

    public function test_cannot_deserialize_scalar()
    {
        $object = new ListOf(new IntType());

        $this->expectException(\InvalidArgumentException::class);
        $object->deserialize('i am string');
    }

    public function test_can_throw_exception_on_empty_list()
    {
        $object = (new ListOf(new IntType()))->allowEmpty(false);

        $this->expectException(\InvalidArgumentException::class);
        $object->deserialize([]);
    }

    public function test_list_data_is_countable()
    {
        $object = new ListOf(new IntType());

        $object->deserialize([1, 2, 3]);
        $this->assertCount(3, $object);
    }

    public function test_list_data_is_iterable()
    {
        $object = new ListOf(new IntType());

        $values = [3, 1, 2];

        $object->deserialize($values);

        foreach ($object as $value) {
            $this->assertContains($value, $values);
        }
    }
}