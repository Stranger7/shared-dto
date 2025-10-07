<?php

namespace DTOCompiler\compilers\ts\properties;

use Exception;

class JsonProperty extends AbstractProperty
{
    /**
     * @throws Exception
     */
    protected function getType(): string
    {
        return '{}';
    }
}
