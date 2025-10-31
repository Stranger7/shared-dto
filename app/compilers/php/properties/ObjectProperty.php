<?php

namespace DTOCompiler\compilers\php\properties;

use DTOCompiler\compilers\php\PHPHelper;
use DTOCompiler\models\PropertyDescriptor;
use yii\helpers\Inflector;

class ObjectProperty extends AbstractProperty
{
    private string $type;

    public function __construct(PropertyDescriptor $property)
    {
        parent::__construct($property);
        $this->type = PHPHelper::makeFullClassName($this->property->typeOf ?? '');
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
            Inflector::camelize($this->property->name),
            $this->type,
            $this->property->name
        ], $template);
    }
}
