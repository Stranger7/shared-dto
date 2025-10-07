<?php

namespace DTOCompiler\compilers\ts\properties;

class IntegerProperty extends AbstractProperty
{
    protected function getType(): string
    {
        return 'number';
    }
}
