<?php

namespace DTOCompiler\compilers\php\properties;

class StringProperty extends AbstractProperty
{
    protected function renderType(): string
    {
        return 'string';
    }

    protected function renderDefault(): string
    {
        return "'" . $this->property->default . "'";
    }

    protected function renderAttributes(): string
    {
        $code = parent::renderAttributes();

        if ($this->property->typeOf) {
            switch ($this->property->typeOf) {
                case 'email':
                    $code .= $this->renderRuleEmail();
                    break;
                case 'datetime':
                    $code .= $this->renderRuleDateTime();
            }
        }

        return $code;
    }

    protected function renderRuleEmail(): string
    {
        $this->imports[] = 'use Yiisoft\Validator\Rule\Email;';

        return self::INDENT . '#[Email]' . PHP_EOL;
    }

    protected function renderRuleDateTime(): string
    {
        $this->imports[] = 'use Yiisoft\Validator\Rule\DateTime;';
        $format = $this->property->format ?? 'Y-m-d\TH:i:s.uP';

        return self::INDENT . "#[DateTime(format: '$format')]" . PHP_EOL;
    }
}
