<?php

namespace DTOCompiler\compilers\ts\properties;

class FloatProperty extends AbstractProperty
{
    protected function getType(): string
    {
        return 'number';
    }
}
