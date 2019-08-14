<?php

namespace Differ\GenDiff;

use function Differ\FileParser\parseFileContent;
use function Differ\AST\makeAst;
use function Differ\ReportGenerator\generateReport;

const AVAILABLE_EXTENSIONS = ["json", "yaml"];

function genDiff(string $firstFilePath, string $secondFilePath, string $format)
{
    if (!(file_exists($firstFilePath) && file_exists($secondFilePath))) {
        throw new \Exception('Error: one of files does not exists.');
    }
    $firstFileInfo = pathinfo($firstFilePath);
    $secondFileInfo = pathinfo($secondFilePath);
    $firstFileExtension = $firstFileInfo['extension'];
    $secondFileExtension = $secondFileInfo['extension'];
    if (!($firstFileExtension === $secondFileExtension)) {
        throw new \Exception('Error: files extensions are not the same.');
    } elseif (!in_array($firstFileExtension, AVAILABLE_EXTENSIONS)) {
        throw new \Exception("Error: {$firstFileExtension} extension is unsupported.");
    }
    $firstFileContent = file_get_contents($firstFilePath);
    $secondFileContent = file_get_contents($secondFilePath);
    $firstFileParsedContent = parseFileContent($firstFileContent, $firstFileExtension);
    $secondFileParsedContent = parseFileContent($secondFileContent, $secondFileExtension);
    $ast = makeAst($firstFileParsedContent, $secondFileParsedContent);
    $report = generateReport($ast, $format);
    return $report;
}
