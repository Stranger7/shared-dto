<?php

namespace DTOCompiler\compilers\ts\properties;

class StringProperty extends AbstractProperty
{
    protected function getType(): string
    {
        return 'string';
    }
}
