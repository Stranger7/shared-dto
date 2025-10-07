<?php

namespace dto;

use ReflectionException;
use Yiisoft\Validator\Helper\DataSetNormalizer;
use Yiisoft\Validator\Helper\RulesNormalizer;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Validator;

class Input extends AbstractDto implements InputInterface
{
    /**
     * @throws ReflectionException
     */
    public function validate(): Result
    {
        return (new Validator())->validate($this);
    }

    /**
     * @throws ReflectionException
     */
    public function getValidateRules(): array
    {
        return RulesNormalizer::normalize(null, DataSetNormalizer::normalize($this));
    }
}
