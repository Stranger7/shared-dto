<?php

namespace DTOCompiler\models;

use dto\AbstractDto;

class PropertyRenderData extends AbstractDto
{
    public string $definition;
    public ?string $setter = null;
}
