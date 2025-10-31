<?php

namespace DTOCompiler\compilers\php\properties;

use DTOCompiler\models\PropertyDescriptor;
use yii\helpers\Inflector;

abstract class AbstractProperty
{
    public const string INDENT = '    ';

    protected PropertyDescriptor $property;
    protected array $imports = [];

    public function __construct(PropertyDescriptor $property, protected bool $required = false)
    {
        $this->property = $property;
    }

    public function renderDefinition(): string
    {
        $comment = $this->renderComment();
        $code = ($comment ? $comment . PHP_EOL : '');
        $code .= $this->renderAttributes();

        $code .= self::INDENT . 'public '
            . ($this->required ? '' : '?')
            . $this->renderType() . ' $' . Inflector::variablize($this->property->name)
            . (!is_null($this->property->default)
                ? ' = ' . $this->renderDefault()
                : ($this->required ? '' : ' = null'))
            . ';';

        return $code;
    }

    public function renderSetter(): string
    {
        return '';
    }

    public function getImports(): array
    {
        return array_unique($this->imports);
    }

    protected function renderAttributes(): string
    {
        return $this->renderRuleRequired();
    }

    protected function renderComment(): string
    {
        return $this->property->comment ? self::INDENT . '// ' . $this->property->comment : '';
    }

    protected function renderRuleRequired(): string
    {
        $import = 'use Yiisoft\Validator\Rule\Required;';
        if ($this->required) {
            if (!in_array($import, $this->imports)) {
                $this->imports[] = $import;
            }
            return self::INDENT . '#[Required]' . PHP_EOL;
        }

        return '';
    }

    abstract protected function renderType(): string;

    abstract protected function renderDefault(): string;
}
