<?php

namespace DTOCompiler\compilers\ts\properties;

use DTOCompiler\compilers\PropertyInterface;
use DTOCompiler\helpers\StringHelper;
use DTOCompiler\models\Descriptor;
use DTOCompiler\models\PropertyRenderData;

abstract class AbstractProperty implements PropertyInterface
{
    protected Descriptor $property;

    public function __construct(Descriptor $property)
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
            . '  ' . StringHelper::variablize($this->property->name)
            . (!$this->property->required ? '?' : '')
            . ': '
            . $this->getType()
            . ';';
    }
}
