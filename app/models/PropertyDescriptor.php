<?php

namespace DTOCompiler\models;

use dto\AbstractDto;

class PropertyDescriptor extends AbstractDto
{
    public string $name;
    public string $type;
    public ?string $typeOf = null;
    public ?string $format = null;
    public mixed $default = null;
    public ?string $comment = null;
}
