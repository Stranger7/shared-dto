<?php

namespace DTOCompiler\compilers\ts;

use Exception;
use yii\helpers\Inflector;

final class ImportStringMaker
{
    private static ?ImportStringMaker $instance = null;
    private array $imports;
    private string $moduleFolder;

    /**
     * gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance(): ImportStringMaker
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public function init(string $moduleFolder): void
    {
        $this->moduleFolder = $moduleFolder;
        $this->imports = [];
    }

    public function make(?string $import): ?string
    {
        if (!$import || in_array($import, ['dto', 'input', 'success', 'debug', 'output', 'error'])) {
            return null;
        }

        if (isset($this->imports[$import]['name'])) {
            return $this->imports[$import]['name'];
        }

        $pieces = explode('\\', $import);
        $type = Inflector::camelize(array_pop($pieces));
        $i = 0;
        while (isset($this->imports[$type])) {
            $type .= ++$i;
        }

        $this->imports[$import] = 'import type { ' . $type . ' } from \'' . $this->calcImportPath($import) . '\';';

        return $type;
    }

    public function list(): array
    {
        return $this->imports;
    }

    private function calcImportPath(string $import): string
    {
        $importSegments = explode('\\', $import);
        $importFile = array_pop($importSegments);
        $folderSegments = explode('/', $this->moduleFolder);

        // Сократим общую часть
        while ($importSegments && $folderSegments && $importSegments[0] === $folderSegments[0]) {
            array_shift($importSegments);
            array_shift($folderSegments);
        }

        $countImportSegments = count($importSegments);
        $countFolderSegments = count($folderSegments);

        if (!$countFolderSegments && $countImportSegments) {
            $path = './' . implode('/', $importSegments);
        } elseif ($countFolderSegments && !$countImportSegments) {
            $path = rtrim(str_repeat('../', $countFolderSegments), '/');
        } elseif (!$countFolderSegments && !$countImportSegments) {
            $path = '.';
        } else {
            $path = str_repeat('../', $countFolderSegments) . implode('/', $importSegments);
        }

        return $path . '/' . $importFile;
    }
}
