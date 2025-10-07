<?php

namespace DTOCompiler\compilers\ts\properties;

use DTOCompiler\compilers\ts\ImportService;
use Exception;

class ObjectProperty extends AbstractProperty
{
    /**
     * @throws Exception
     */
    protected function getType(): string
    {
        if (!$this->property->typeOf || $this->property->typeOf === 'any') {
            return '{}';
        }

        if ($type = ImportService::getInstance()->createType($this->property->typeOf)) {
            return $type;
        }
        
        return $this->property->typeOf;
    }
}
