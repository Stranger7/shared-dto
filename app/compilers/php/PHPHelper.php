<?php

namespace DTOCompiler\compilers\php;

use DTOCompiler\CompilerHelper;
use yii\helpers\Inflector;

class PHPHelper
{
    public const string PHP_DTO_NAMESPACE = 'dto';

    public static function getDtoRootFolder(): string
    {
        return realpath(env('PHP_DTO_FOLDER', CompilerHelper::getDefaultDtoRoot() . DIRECTORY_SEPARATOR . '/php'));
    }

    public static function getParentClass(?string $parent): string
    {
        if (!$parent) {
            return '\dto\AbstractDto';
        }

        return self::makeNamespace($parent);
    }

    public static function makeNamespace(string $path): string
    {
        $path = trim(str_replace('/', '\\', $path), '\\') ;

        return trim(
            self::PHP_DTO_NAMESPACE . '\\'
            . implode(
                '\\',
                array_map(
                    static fn(string $item) => Inflector::camelize($item),
                    preg_split('/\\\/', $path),
                ),
            ),
            '\\',
        );
    }

    public static function makeFullClassName(string $className): string
    {
        return '\\' . self::makeNamespace($className);
    }
}
