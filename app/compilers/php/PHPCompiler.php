<?php

namespace DTOCompiler\compilers\php;

use DTOCompiler\compilers\AbstractCompiler;
use DTOCompiler\compilers\php\properties\AbstractProperty;
use DTOCompiler\compilers\php\properties\ArrayProperty;
use DTOCompiler\compilers\php\properties\BooleanProperty;
use DTOCompiler\compilers\php\properties\FloatProperty;
use DTOCompiler\compilers\php\properties\IntProperty;
use DTOCompiler\compilers\php\properties\JsonProperty;
use DTOCompiler\compilers\php\properties\ObjectProperty;
use DTOCompiler\compilers\php\properties\StringProperty;
use DTOCompiler\models\DTOData;
use Exception;
use yii\helpers\Inflector;

class PHPCompiler extends AbstractCompiler
{
    private array $rendererMap = [
        'string' => StringProperty::class,
        'int' => IntProperty::class,
        'integer' => IntProperty::class,
        'bool' => BooleanProperty::class,
        'boolean' => BooleanProperty::class,
        'object' => ObjectProperty::class,
        'array' => ArrayProperty::class,
        'float' => FloatProperty::class,
        'double' => FloatProperty::class,
        'json' => JsonProperty::class,
    ];

    private array $imports = [];

    /**
     * @throws Exception
     */
    protected function render(DTOData $data): void
    {
        $properties = $this->renderProperties($data);

        $this->dtoCode = $this->renderHeader($data)
            . $properties['definitions']
            . ($properties['setters'] ? PHP_EOL . PHP_EOL . $properties['setters'] : '') . PHP_EOL
            . '}' . PHP_EOL;
    }

    protected function renderHeader(DTOData $data): string
    {
        $placeholders = [
            '%namespace%' => PHPHelper::makeNamespace($data->path),
            '%className%' => Inflector::camelize($data->name),
            '%parentClass%' => PHPHelper::getParentClass($data->description->parent),
        ];

        $imports = implode(PHP_EOL, $this->imports);
        if ($imports) {
            $imports .= PHP_EOL . PHP_EOL;
        }

        $template = '<?php' . PHP_EOL . PHP_EOL
            . 'namespace {%namespace%};' . PHP_EOL . PHP_EOL
            . $imports
            . 'class {%className%} extends \{%parentClass%}' . PHP_EOL
            . '{' . PHP_EOL;

        return str_replace(
            array_map(
                static fn(string $key) => '{' . $key . '}',
                array_keys($placeholders)
            ),
            array_values($placeholders),
            $template,
        );
    }

    /**
     * @throws Exception
     */
    private function renderProperties(DTOData $data): array
    {
        $definitions = [];
        $setters = [];
        $this->imports = [];

        foreach ($description->properties ?? [] as $property) {
            if (isset($this->rendererMap[$property->type])) {
                /** @var AbstractProperty $renderer */
                $renderer = new $this->rendererMap[$property->type](
                    $property,
                    in_array($property->name, $data->description->required)
                );
                $definitions[] = $renderer->renderDefinition();
                $setters[] = rtrim($renderer->renderSetter());
                $this->imports = array_unique(array_merge($this->imports, $renderer->getImports()));
            } else {
                throw new Exception('PHP: ' . $property->type . ' not defined');
            }
        }

        return [
            'definitions' => implode(PHP_EOL . PHP_EOL, $definitions),
            'setters' => implode(PHP_EOL . PHP_EOL, array_filter($setters)),
        ];
    }

    protected function makeDTOFilename(DTOData $data): string
    {
        $path = implode(
            DIRECTORY_SEPARATOR,
            array_map(
                static fn(string $segment) => Inflector::camelize($segment),
                array_filter(preg_split('/[\/\\\\]/', $data->path . DIRECTORY_SEPARATOR . $data->name)),
            ),
        );

        return $this->dtoRootFolder . $path . '.php';
    }
}
