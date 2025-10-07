<?php

namespace DTOCompiler\compilers;

use DTOCompiler\models\Description;

abstract class AbstractCompiler implements CompilerInterface
{
    protected Description $description;
    protected string $code = '';

    protected string $dstFolder;

    abstract protected function getDtoFilename(): string;

    abstract protected function renderHeader(): string;

    abstract protected function renderProperties(): array;

    abstract protected function setDtoFolder(): void;

    public function __construct(Description $description)
    {
        $this->description = $description;
        $this->setDtoFolder();
    }

    public function save(): void
    {
        $dtoFilename = $this->getDtoFilename();
        $this->ensureFolder(pathinfo($dtoFilename)['dirname']);
        file_put_contents($dtoFilename, $this->code);
    }

    private function ensureFolder(string $folder): void
    {
        $path = '';
        foreach (explode('/', $folder) as $segment) {
            $path .= '/' . $segment;
            if (!is_dir($path)) {
                mkdir($path);
            }
        }
    }
}
