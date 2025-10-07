<?php

namespace DTOCompiler\compilers\ts\properties;

use DTOCompiler\compilers\ts\ImportService;
use Exception;

class ArrayProperty extends AbstractProperty
{
    /**
     * @throws Exception
     */
    protected function getType(): string
    {
        if (!$this->property->typeOf) {
            throw new Exception('Для поля ' . $this->property->name . ' не указан параметр typeOf');
        }

        if (in_array($this->property->typeOf, ['integer', 'float', 'double'])) {
            return 'number[]';
        } elseif (in_array($this->property->typeOf, ['string', 'boolean'])) {
            return $this->property->typeOf . '[]';
        }

        return ImportService::getInstance()->createType($this->property->typeOf) . '[]';
    }
}
