<?php

namespace DTOCompiler\compilers\php\properties;

class BooleanProperty extends AbstractProperty
{
    protected function renderType(): string
    {
        return 'bool';
    }

    protected function renderDefault(): string
    {
        return $this->property->default ? 'true' : 'false';
    }
}
