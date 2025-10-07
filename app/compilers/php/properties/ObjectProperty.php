<?php

namespace DTOCompiler\compilers\php\properties;

use DTOCompiler\helpers\StringHelper;
use DTOCompiler\models\Descriptor;

class ObjectProperty extends AbstractProperty
{
    private string $type;

    public function __construct(Descriptor $property)
    {
        parent::__construct($property);
        $this->type = StringHelper::asFullClassName($this->property->typeOf ?? '');
    }

    protected function renderType(): string
    {
        return $this->type;
    }

    protected function renderDefault(): string
    {
        return $this->property->default;
    }

    public function renderSetter(): string
    {
        $template = self::INDENT . 'public function set{%Capitalized%}(mixed $value): void
    {
        $this->{%name%} = new {%type%}($value);
    }';
        return str_replace([
            '{%Capitalized%}',
            '{%type%}',
            '{%name%}'
        ], [
            StringHelper::camelize($this->property->name),
            $this->type,
            $this->property->name
        ], $template);
    }
}
