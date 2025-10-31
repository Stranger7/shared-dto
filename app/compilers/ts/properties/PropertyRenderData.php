<?php

namespace DTOCompiler\compilers\ts\properties;

use dto\AbstractDto;

class PropertyRenderData extends AbstractDto
{
    public string $definition;
    public ?string $setter = null;
}
