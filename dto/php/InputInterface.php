<?php

namespace dto;

use Yiisoft\Validator\Result;

interface InputInterface
{
    public function validate(): Result;

    public function getValidateRules(): array;
}
