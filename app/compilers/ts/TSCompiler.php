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
use DTOCompiler\models\DTOData;
use yii\helpers\Inflector;

class TSCompiler extends AbstractCompiler
{
    private array $rendersMap = [
        'integer' => IntegerProperty::class,
        'int' => IntegerProperty::class,
        'string' => StringProperty::class,
        'boolean' => BooleanProperty::class,
        'bool' => BooleanProperty::class,
        'float' => FloatProperty::class,
        'double' => DoubleProperty::class,
        'object' => ObjectProperty::class,
        'array' => ArrayProperty::class,
        'json' => JsonProperty::class,
    ];

    protected function renderHeader(DTOData $data): string
    {
        $code = 'export type ' . Inflector::camelize($data->name) . ' = ';

        $type = ImportStringMaker::getInstance()->make($data->description->parent);
        if ($type) {
            $code .= $type . ' & ';
        }

        return $code . '{' . PHP_EOL;
    }

    protected function renderProperties(DTOData $data): array
    {
        $definitions = [];

        foreach ($data->description->properties ?? [] as $property) {
            if (isset($this->rendersMap[$property->type])) {
                /** @var AbstractProperty $renderer */
                $renderer = new $this->rendersMap[$property->type](
                    $property,
                    in_array($property->name, $data->description->required),
                );
                $definitions[] = $renderer->renderDefinition();
            }
        }

        return [
            'definitions' => implode(PHP_EOL . PHP_EOL, $definitions),
            'imports' => implode(PHP_EOL, array_filter(ImportStringMaker::getInstance()->list()))
        ];
    }

    public function makeDTOFilename(DTOData $data): string
    {
        return $this->dtoRootFolder
            . '/' . $data->path
            . '/' . $data->name . '.ts';
    }

    protected function render(DTOData $data): void
    {
        ImportStringMaker::getInstance()->init(trim($data->path, '\\/'));
        $header = $this->renderHeader($data);
        $properties = $this->renderProperties($data);

        $this->dtoCode = ($properties['imports'] ? $properties['imports'] . PHP_EOL . PHP_EOL : '')
            . $header
            . $properties['definitions'] . PHP_EOL
            . '}' . PHP_EOL;
    }
}
