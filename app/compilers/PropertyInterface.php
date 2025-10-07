<?php

namespace DTOCompiler\compilers;

use DTOCompiler\models\PropertyRenderData;

interface PropertyInterface
{
    public function render(): PropertyRenderData;
}
