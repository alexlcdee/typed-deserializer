<?php

namespace AlexLcDee\TypedDeserializer\TypeSystem;


abstract class Type implements \JsonSerializable
{
    abstract public function deserialize($value);
}