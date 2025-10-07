<?php

namespace DTOCompiler\models;

use dto\AbstractDto;
use Exception;
use Symfony\Component\Yaml\Yaml;

class Description extends AbstractDto
{
    public string $folder;
    public string $filename;
    public ?string $parent = null;
    public array $imports = [];

    /** @var Descriptor[] */
    public array $properties = [];

    public function setTypeOf(?string $parent = null): void
    {
        if ($parent !== null) {
            $this->parent = str_replace('/', '\\', $parent);
        }
    }

    public function setProperties(array $properties): void
    {
        $this->properties = array_merge(
            $this->properties,
            array_map(
                function ($property) {
                    return new Descriptor($property);
                },
                $properties
            )
        );
    }

    /**
     * @throws Exception
     */
    public function setImports(array $imports): void
    {
        $this->imports = $imports;
        foreach ($this->imports as $import) {
            $import = trim(str_replace('\\', '/', $import), '/');
            $yaml = (array)Yaml::parseFile(realpath(__DIR__ . '/../../source/' . $import . '.yaml'));
            $description = new Description($yaml);
            if ($description->parent !== 'dto') {
                throw new Exception('The imported yaml must be a child of "dto"');
            }
            $this->setProperties($description->properties);
        }
    }
}
