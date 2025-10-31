<?php

namespace DTOCompiler\models;

use dto\AbstractDto;
use DTOCompiler\CompilerHelper;
use Exception;
use Symfony\Component\Yaml\Yaml;

class DTODescription extends AbstractDto
{
    public string $description = '';

    public string $parent = '';

    public array $required = [];

    public array $imports = [];

    /** @var PropertyDescriptor[] */
    public array $properties = [];

    public function setProperties(array $properties): void
    {
        $this->properties = array_merge(
            $this->properties,
            array_map(
                function ($property) {
                    return new PropertyDescriptor($property);
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
            $yaml = (array)Yaml::parseFile(CompilerHelper::getFilenameFromImport($import));
            $description = new DTODescription($yaml);
            if ($description->parent !== 'dto') {
                throw new Exception('The imported yaml must be a child of "dto"');
            }
            $this->setProperties($description->properties);
        }
    }
}
