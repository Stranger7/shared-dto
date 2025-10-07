<?php

namespace DTOCompiler\compilers\php\properties;

use DTOCompiler\helpers\StringHelper;
use DTOCompiler\models\Descriptor;

abstract class AbstractProperty
{
    public const string INDENT = '    ';

    protected Descriptor $property;
    protected array $imports = [];

    public function __construct(Descriptor $property)
    {
        $this->property = $property;
    }

    public function renderDefinition(): string
    {
        $comment = $this->renderComment();
        $code = ($comment ? $comment . PHP_EOL : '');
        $code .= $this->renderAttributes();

        $code .= self::INDENT . 'public '
            . ($this->property->required ? '' : '?')
            . $this->renderType() . ' $' . StringHelper::variablize($this->property->name)
            . (!is_null($this->property->default)
                ? ' = ' . $this->renderDefault()
                : ($this->property->required ? '' : ' = null'))
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
        if ($this->property->required) {
            $this->imports[] = 'use Yiisoft\Validator\Rule\Required;';
            return self::INDENT . '#[Required]' . PHP_EOL;
        }

        return '';
    }

    abstract protected function renderType(): string;

    abstract protected function renderDefault(): string;
}
