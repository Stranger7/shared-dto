<?php

namespace DTOCompiler\compilers\ts;

use DTOCompiler\CompilerHelper;

class TSHelper
{
    public static function getDtoRootFolder(): string
    {
        return realpath(env('PHP_DTO_FOLDER', CompilerHelper::getDefaultDtoRoot() . DIRECTORY_SEPARATOR . '/ts'));
    }
}
