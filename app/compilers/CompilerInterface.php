<?php

namespace DTOCompiler\compilers;

use DTOCompiler\models\DTOData;

interface CompilerInterface
{
    public function run(DTOData $data): void;
}
