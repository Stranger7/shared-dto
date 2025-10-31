<?php

namespace DTOCompiler;

class CompilerHelper
{
    public static function getDefaultDtoRoot(): string
    {
        return realpath(__DIR__ . '/../dto');
    }

    public static function getSrcRootFolder(): string
    {
        return realpath(env('SOURCE_FOLDER', self::getDefaultDtoRoot() . DIRECTORY_SEPARATOR . '/source'));
    }

    public static function getFilenameFromImport(string $import): string
    {
        $import = trim(str_replace('\\', '/', $import), '/');

        return self::getSrcRootFolder() . '/' . $import . '.yaml';
    }
}
