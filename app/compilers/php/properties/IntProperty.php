<?php

namespace DTOCompiler\compilers\php\properties;

class IntProperty extends AbstractProperty
{
    protected function renderType(): string
    {
        return 'int';
    }

    protected function renderDefault(): string
    {
        return $this->property->default;
    }
}
