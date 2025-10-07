<?php

namespace DTOCompiler\helpers;

class StringHelper
{
    /**
     * Returns given word as CamelCased.
     *
     * Converts a word like "send_email" to "SendEmail"
     * "who's online" will be converted to "WhoSOnline".
     */
    public static function camelize(string $string): string
    {
        if (empty($string)) {
            return $string;
        }
        return str_replace(' ', '', ucwords(preg_replace('/[^\pL\pN]+/u', ' ', $string)));
    }

    public static function variablize(string $word): string
    {
        if (empty($word)) {
            return $word;
        }
        $word = static::camelize($word);

        return strtolower(substr($word, 0, 1)) . substr($word, 1, null);
    }

    public static function asImportString(string $className): string
    {
        return rtrim(env('PHP_DTO_NAMESPACE') . '\\'
            . implode(
                '\\',
                array_map(
                    static fn(string $item) => StringHelper::camelize($item),
                    preg_split('/\\\|\//', $className)
                )
            ), '\\');
    }

    public static function asFullClassName(string $className): string
    {
        return '\\' . StringHelper::asImportString($className);
    }
}
