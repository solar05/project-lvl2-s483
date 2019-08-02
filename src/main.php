<?php

namespace Gendiff\Main;

use function cli\line;
use function Gendiff\Utils\FileUtils\isFilesExists;
use function Gendiff\FileParser\parseFiles;
use function Gendiff\AST\makeAst;
use function Gendiff\ReportGenerator\generateReport;

const AVAILABLE_EXTENSIONS = ["json", "yaml"];

function runGendiff($args)
{
    $format = $args->args['--format'];
    [$firstFileName, $secondFileName] = $args->args['<file>'];
    $pathToFiles = $args->args['--path'];
    $firstFileFullPath = "{$pathToFiles}{$firstFileName}";
    $secondFileFullPath = "{$pathToFiles}{$secondFileName}";
    [, $firstFileExtension] = explode('.', $firstFileName);
    [, $secondFileExtension] = explode('.', $secondFileName);
    try {
        if (!isFilesExists($firstFileFullPath, $secondFileFullPath)) {
            throw new \Exception('Error: one of files does not exists.');
        } elseif (!($firstFileExtension === $secondFileExtension)) {
            throw new \Exception('Error: files extensions are not the same.');
        } elseif (!in_array($firstFileExtension, AVAILABLE_EXTENSIONS)) {
            throw new \Exception("Error: {$firstFileExtension} extension is unsupported.");
        }
        $parsedData = parseFiles($firstFileFullPath, $secondFileFullPath, $firstFileExtension);
        $ast = makeAst(...$parsedData);
        $report = generateReport($format, $ast);
    } catch (\Exception $error) {
        line($error);
        return;
    }
    line($report);
}
