<?php

namespace DTOCompiler\compilers;

use DTOCompiler\models\DTOData;

abstract class AbstractCompiler implements CompilerInterface
{
    protected string $dtoCode = '';

    public function __construct(protected string $dtoRootFolder)
    {
        $this->dtoRootFolder = rtrim($this->dtoRootFolder, '\\/') . DIRECTORY_SEPARATOR;
    }

    public function run(DTOData $data): void
    {
        $this->render($data);
        $this->save($this->makeDTOFilename($data));
    }

    abstract protected function render(DTOData $data): void;

    abstract protected function makeDTOFilename(DTOData $data): string;

    protected function save(string $filename): void
    {
        $this->ensureFolder(pathinfo($filename)['dirname']);
        file_put_contents($filename, $this->dtoCode);
    }

    private function ensureFolder(string $folder): void
    {
        $path = '';
        foreach (explode(DIRECTORY_SEPARATOR, $folder) as $segment) {
            $path .= DIRECTORY_SEPARATOR . $segment;
            if (!is_dir($path)) {
                mkdir($path);
            }
        }
    }
}
