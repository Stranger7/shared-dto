<?php

namespace DTOCompiler;

use DTOCompiler\compilers\php\PHPCompiler;
use DTOCompiler\compilers\ts\TypeScriptCompiler;
use DTOCompiler\helpers\Console;
use DTOCompiler\models\Description;
use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class Compiler
{
    private string $srcFolder;
    private string $subFolder;

    public function __construct(string $subFolder = '')
    {
        $this->subFolder = DIRECTORY_SEPARATOR . trim($subFolder, '\\/');
        $this->srcFolder = realpath(__DIR__ . '/../source/');
    }

    public function run(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->srcFolder . $this->subFolder, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (strtolower($file->getExtension()) === 'yaml') {
                try {
                    $yaml = (array)Yaml::parseFile($file->getRealPath());
                    $description = new Description($yaml);

                    $description->folder = substr($file->getPath(), strlen($this->srcFolder) + 1);
                    $description->filename = $file->getBasename('.yaml');

                    Console::stdout('PHP: ' .  $file->getRealPath() . ' ... ');
                    (new PHPCompiler($description))->render()->save();
                    Console::success('Ok!');

                    Console::stdout('TS: ' .  $file->getRealPath() . ' ... ');
                    (new TypeScriptCompiler($description))->render()->save();
                    Console::success('Ok!');
                } catch (Exception $exception) {
                    Console::error('Error: ' .  $exception->getMessage());
                    Console::error('Error: ' .  $exception->getTraceAsString());
                    return;
                }
            }
        }
    }
}
