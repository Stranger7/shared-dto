<?php

namespace DTOCompiler\compilers\ts\properties;

class BooleanProperty extends AbstractProperty
{
    protected function getType(): string
    {
        return 'boolean';
    }
}
