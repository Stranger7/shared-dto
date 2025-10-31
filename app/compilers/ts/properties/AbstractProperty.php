<?php

namespace DTOCompiler\compilers\ts\properties;

use DTOCompiler\compilers\PropertyInterface;
use DTOCompiler\models\PropertyDescriptor;
use yii\helpers\Inflector;

abstract class AbstractProperty implements PropertyInterface
{
    protected PropertyDescriptor $property;

    public function __construct(PropertyDescriptor $property, protected bool $required = false)
    {
        $this->property = $property;
    }

    abstract protected function getType(): string;

    public function render(): PropertyRenderData
    {
        return new PropertyRenderData([
            'definition' => $this->renderDefinition()
        ]);
    }

    public function renderDefinition(): string
    {
        return ($this->property->comment ? ('  // ' . $this->property->comment . PHP_EOL) : '')
            . '  ' . Inflector::variablize($this->property->name)
            . (!$this->required ? '?' : '')
            . ': '
            . $this->getType()
            . ';';
    }
}
