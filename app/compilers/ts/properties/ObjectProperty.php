<?php

namespace DTOCompiler\compilers\ts\properties;

use DTOCompiler\compilers\ts\ImportStringMaker;
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

        if ($type = ImportStringMaker::getInstance()->make($this->property->typeOf)) {
            return $type;
        }
        
        return $this->property->typeOf;
    }
}
