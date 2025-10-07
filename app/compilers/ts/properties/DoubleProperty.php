<?php

namespace DTOCompiler\compilers\ts\properties;

class DoubleProperty extends AbstractProperty
{
    protected function getType(): string
    {
        return 'number';
    }
}
