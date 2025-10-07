<?php

namespace DTOCompiler\compilers\php\properties;

class FloatProperty extends AbstractProperty
{
    protected function renderType(): string
    {
        return 'float';
    }

    protected function renderDefault(): string
    {
        return $this->property->default;
    }
}
