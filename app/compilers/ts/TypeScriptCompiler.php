<?php

namespace DTOCompiler\compilers\ts;

use DTOCompiler\compilers\AbstractCompiler;
use DTOCompiler\compilers\ts\properties\AbstractProperty;
use DTOCompiler\compilers\ts\properties\ArrayProperty;
use DTOCompiler\compilers\ts\properties\BooleanProperty;
use DTOCompiler\compilers\ts\properties\DoubleProperty;
use DTOCompiler\compilers\ts\properties\FloatProperty;
use DTOCompiler\compilers\ts\properties\IntegerProperty;
use DTOCompiler\compilers\ts\properties\JsonProperty;
use DTOCompiler\compilers\ts\properties\ObjectProperty;
use DTOCompiler\compilers\ts\properties\StringProperty;
use DTOCompiler\helpers\StringHelper;
use DTOCompiler\models\Description;

/*
 * 1. Простой
 *
export type NewTopicsTopic = {
  id: number;
  slug: string;
  topic: string;
  pinned?: boolean;
  postedAt: Date;
  commentsCount: number;
};
 *
 * 2. Вложенные объект
 *
import type { Address } from '<брать из настроек>/example/address';

export type Qwe = {
  isAdmin: boolean
  address: Address
}
 *
 * 3. С наследованием
 *
import type { Parent } from '...'
import type { Address } from '...';

export type Qwe = Parent & {
  isAdmin: boolean
  address: Address
}
 */

class TypeScriptCompiler extends AbstractCompiler
{
    private array $rendersMap = [
        'integer' => IntegerProperty::class,
        'string' => StringProperty::class,
        'boolean' => BooleanProperty::class,
        'float' => FloatProperty::class,
        'double' => DoubleProperty::class,
        'object' => ObjectProperty::class,
        'array' => ArrayProperty::class,
        'json' => JsonProperty::class,
    ];

    public function __construct(Description $description)
    {
        parent::__construct($description);
        ImportService::getInstance()->init($this->description->folder);
    }

    protected function setDtoFolder(): void
    {
        $this->dstFolder = realpath(__DIR__ . '/../../../dto/ts');
    }

    public function render(): self
    {
        $header = $this->renderHeader();
        $properties = $this->renderProperties();

        $this->code = ($properties['imports'] ? $properties['imports'] . PHP_EOL . PHP_EOL : '')
            . $header
            . $properties['definitions'] . PHP_EOL
            . '}' . PHP_EOL;

        return $this;
    }

    protected function getDtoFilename(): string
    {
        return $this->dstFolder
            . '/' . $this->description->folder
            . '/' . $this->description->filename . '.ts';
    }

    /*
     * export type NewTopicsTopic = {
     *
     * import type { Parent } from '..'
     * export type Qwe = Parent & {
     */
    protected function renderHeader(): string
    {
        $code = 'export type ' . StringHelper::camelize($this->description->filename) . ' = ';

        $type = ImportService::getInstance()->createType($this->description->parent);
        if ($type) {
            $code .= $type . ' & ';
        }

        return $code . '{' . PHP_EOL;
    }

    protected function renderProperties(): array
    {
        $definitions = [];

        foreach ($this->description->properties ?? [] as $property) {
            if (isset($this->rendersMap[$property->type])) {
                /** @var AbstractProperty $renderer */
                $renderer = new $this->rendersMap[$property->type]($property);
                $definitions[] = $renderer->renderDefinition();
            }
        }

        return [
            'definitions' => implode(PHP_EOL . PHP_EOL, $definitions),
            'imports' => implode(PHP_EOL, array_filter(ImportService::getInstance()->list()))
        ];
    }
}
