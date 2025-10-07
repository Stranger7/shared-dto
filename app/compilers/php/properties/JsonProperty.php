<?php

namespace DTOCompiler\compilers\php\properties;

class JsonProperty extends AbstractProperty
{
    protected function renderType(): string
    {
        return 'array';
    }

    protected function renderDefault(): string
    {
        return $this->property->default;
    }
}
