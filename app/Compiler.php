<?php

namespace DTOCompiler;

use DTOCompiler\compilers\php\PHPCompiler;
use DTOCompiler\compilers\php\PHPHelper;
use DTOCompiler\compilers\ts\TSCompiler;
use DTOCompiler\compilers\ts\TSHelper;
use DTOCompiler\models\DTOData;
use DTOCompiler\models\DTODescription;
use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use yii\helpers\BaseConsole;
use yii\helpers\Console;

class Compiler
{
    private string $srcRootFolder;
    private string $srcSubFolder;

    private PHPCompiler $phpCompiler;
    private TSCompiler $tsCompiler;

    public function __construct(string $subFolder = '')
    {
        $this->srcSubFolder = DIRECTORY_SEPARATOR . trim($subFolder, '\\/');
        $this->srcRootFolder = CompilerHelper::getSrcRootFolder();
        $this->phpCompiler = new PHPCompiler(PHPHelper::getDtoRootFolder());
        $this->tsCompiler = new TSCompiler(TSHelper::getDtoRootFolder());
    }

    public function run(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->srcRootFolder . $this->srcSubFolder, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD,
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($this->isYaml($file)) {
                try {
                    $this->compile($file);
                } catch (Exception $e) {
                    Console::error($e->getFile() . ' #' . $e->getLine() . ': ' . $e->getMessage());
                    break;
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    private function compile(SplFileInfo $file): void
    {
        $parts = pathinfo($file->getRealPath());
        $srcRelativePath = substr(
            $parts['dirname'],
            strlen($this->srcRootFolder),
        );
        $yaml = (array)Yaml::parseFile($file->getRealPath());

        $dtoData = new DTOData();
        $dtoData->name = $parts['filename'];
        $dtoData->path = $srcRelativePath;
        $dtoData->description = new DTODescription($yaml);

        Console::stdout(
            Console::ansiFormat(
                $srcRelativePath . DIRECTORY_SEPARATOR . $dtoData->name,
                [BaseConsole::FG_CYAN]
            ) . PHP_EOL
        );
        Console::stdout('PHP: ... ');
        $this->phpCompiler->run($dtoData);
        $this->printSuccess();

        Console::stdout('TS:  ... ');
        $this->tsCompiler->run($dtoData);
        $this->printSuccess();
    }

    private function isYaml(SplFileInfo $file): bool
    {
        return strtolower($file->getExtension()) === 'yaml';
    }

    private function printSuccess(): void
    {
        Console::stdout(Console::ansiFormat('Ok!', [BaseConsole::FG_GREEN]) . PHP_EOL);
    }
}
