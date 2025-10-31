<?php

namespace DTOCompiler\models;

use dto\AbstractDto;

class DTOData extends AbstractDTO
{
    public string $name;

    public string $path;

    public DTODescription $description;
}
