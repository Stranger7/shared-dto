<?php

namespace DTOCompiler\models;

use dto\AbstractDto;

class Descriptor extends AbstractDto
{
    public string $name;
    public string $type;
    public ?string $typeOf = null;
    public ?string $format = null;
    public ?bool $required = false;
    public mixed $default = null;
    public ?string $comment = null;

    public function setTypeOf(?string $typeOf = null): void
    {
        if ($typeOf !== null) {
            $this->typeOf = str_replace('/', '\\', $typeOf);
        }
    }
}
