<?php

namespace DTOCompiler\compilers\php\properties;

use DTOCompiler\helpers\StringHelper;

class ArrayProperty extends AbstractProperty
{
    protected function renderType(): string
    {
        return 'array';
    }

    protected function renderDefault(): string
    {
        return '[' . implode(',', $this->property->default) . ']';
    }

    protected function renderComment(): string
    {
        $parts = [];
        if ($typeOf = $this->property->typeOf) {
            if (is_int(strpos($typeOf, '\\'))) {
                $typeOf = StringHelper::asFullClassName($typeOf);
            }
            $parts[] = $typeOf . '[]';
        }
        if ($this->property->comment) {
            $parts[] = $this->property->comment;
        }
        if ($parts) {
            return self::INDENT . ($typeOf ? '/** @var ' : '/* ') . implode(' ', $parts) . ' */';
        }

        return '';
    }

    public function renderSetter(): string
    {
        if ($this->property->typeOf && is_int(strpos($this->property->typeOf, '\\'))) {
            $template = self::INDENT . 'public function set{%Capitalized%}(mixed $value): void
    {
        $this->{%name%} = [];
        foreach ((array)$value as $item) {
            $this->{%name%}[] = new {%typeOf%}($item);
        }
    }';
            return str_replace([
                '{%Capitalized%}',
                '{%typeOf%}',
                '{%name%}'
            ], [
                StringHelper::camelize($this->property->name),
                StringHelper::asFullClassName($this->property->typeOf),
                $this->property->name
            ], $template);
        }

        return '';
    }
}
