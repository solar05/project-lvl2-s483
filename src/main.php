<?php

namespace Gendiff\Main;

use function cli\line;
use function Gendiff\Utils\FileUtils\isFilesExtensionSame;
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
    if (!isFilesExists($firstFileFullPath, $secondFileFullPath)) {
        line('Error: one of files does not exists.');
        return;
    } elseif (!($firstFileExtension === $secondFileExtension)) {
        line('Error: files extensions are not the same.');
        return;
    } elseif (!in_array($firstFileExtension, AVAILABLE_EXTENSIONS)) {
        line("Error: {$firstFileExtension} extension is unsupported.");
        return;
    }
    try {
        $parsedData = parseFiles($firstFileFullPath, $secondFileFullPath, $firstFileExtension);
        $ast = makeAst(...$parsedData);
        $report = generateReport($format, $ast);
    } catch (\Exception $error) {
        line($error);
        return;
    }
    line($report);
}
