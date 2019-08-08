<?php

namespace Gendiff\Main;

use function Funct\Collection\last;
use function Gendiff\Utils\FileUtils\isFilesExists;
use function Gendiff\FileParser\parseFiles;
use function Gendiff\AST\makeAst;
use function Gendiff\ReportGenerator\generateReport;

const AVAILABLE_EXTENSIONS = ["json", "yaml"];

function runGendiff(string $format, string $firstFilePath, string $secondFilePath)
{
    $firstFileExtension = last(explode('.', $firstFilePath));
    $secondFileExtension = last(explode('.', $secondFilePath));
    try {
        if (!isFilesExists($firstFilePath, $secondFilePath)) {
            throw new \Exception('Error: one of files does not exists.');
        } elseif (!($firstFileExtension === $secondFileExtension)) {
            throw new \Exception('Error: files extensions are not the same.');
        } elseif (!in_array($firstFileExtension, AVAILABLE_EXTENSIONS)) {
            throw new \Exception("Error: {$firstFileExtension} extension is unsupported.");
        }
        $parsedData = parseFiles($firstFilePath, $secondFilePath, $firstFileExtension);
        $ast = makeAst(...$parsedData);
        $report = generateReport($format, $ast);
    } catch (\Exception $error) {
        return $error->getMessage();
    }
    return $report;
}
