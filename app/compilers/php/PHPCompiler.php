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
use DTOCompiler\helpers\StringHelper;
use Exception;

class PHPCompiler extends AbstractCompiler
{
    private array $rendererMap = [
        'string' => StringProperty::class,
        'integer' => IntProperty::class,
        'boolean' => BooleanProperty::class,
        'object' => ObjectProperty::class,
        'array' => ArrayProperty::class,
        'float' => FloatProperty::class,
        'json' => JsonProperty::class,
    ];

    private array $imports = [];

    protected function setDtoFolder(): void
    {
        $this->dstFolder = realpath(__DIR__ . '/../../../dto/php');
    }

    /**
     * @throws Exception
     */
    public function render(): self
    {
        $properties = $this->renderProperties();

        $this->code = $this->renderHeader()
            . $properties['definitions']
            . ($properties['setters'] ? PHP_EOL . PHP_EOL . $properties['setters'] : '') . PHP_EOL
            . '}' . PHP_EOL;

        return $this;
    }

    protected function renderHeader(): string
    {
        $placeholders = [
            '%namespace%' => env('PHP_DTO_NAMESPACE'),
            '%classPath%' => StringHelper::asImportString($this->description->folder),
            '%className%' => StringHelper::camelize($this->description->filename),
            '%parentClass%' => $this->getParentClass($this->description->parent),
        ];

        $imports = implode(PHP_EOL, $this->imports);
        if ($imports) {
            $imports .= PHP_EOL . PHP_EOL;
        }

        $template = '<?php' . PHP_EOL . PHP_EOL
            . 'namespace {%classPath%};' . PHP_EOL . PHP_EOL
            . $imports
            . 'class {%className%} extends \{%parentClass%}' . PHP_EOL
            . '{' . PHP_EOL;

        return str_replace(
            array_map(static fn(string $key) => '{' . $key . '}', array_keys($placeholders)),
            array_values($placeholders),
            $template
        );
    }

    /**
     * @throws Exception
     */
    protected function renderProperties(): array
    {
        $definitions = [];
        $setters = [];

        foreach ($this->description->properties ?? [] as $property) {
            if (isset($this->rendererMap[$property->type])) {
                /** @var AbstractProperty $renderer */
                $renderer = new $this->rendererMap[$property->type]($property);
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

    private function getParentClass(?string $parent)
    {
        if (!$parent) {
            return env('PHP_DTO_DEFAULT_PARENT');
        }
        return StringHelper::asImportString($parent);
    }

    protected function getDtoFilename(): string
    {
        $folder = implode(
            '/',
            array_map(
                static fn(string $segment) => StringHelper::camelize($segment),
                explode('/', $this->description->folder)
            )
        );

        return $this->dstFolder
            . '/' . $folder
            . '/' . StringHelper::camelize($this->description->filename) . '.php';
    }
}
