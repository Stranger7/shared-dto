<?php

namespace DTOCompiler\compilers;

use DTOCompiler\compilers\ts\properties\PropertyRenderData;

interface PropertyInterface
{
    public function render(): PropertyRenderData;
}
