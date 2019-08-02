<?php

namespace Gendiff\Main;

use function cli\line;
use Docopt;
use function Gendiff\Utils\FileUtils\isFilesExtensionSame;
use function Gendiff\Utils\FileUtils\isFilesExists;
use Gendiff\FileParser\FileParser;
use Gendiff\AST;
use function Gendiff\ReportGenerator\generateReport;

function run()
{
    $documentation = "
Generate diff

Usage: gendiff --format=<fmt> <file> <file> --path=<path>
gendiff (-h|--help)
gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  --format <fmt>                Report format [default: pretty]
";
    $args = Docopt::handle($documentation, array('version' => 'GenDiff 1.0'));
    $format = $args->args['--format'];
    [$firstFileName, $secondFileName] = $args->args['<file>'];
    $pathToFiles = $args->args['--path'];
    $firstFileFullPath = "{$pathToFiles}{$firstFileName}";
    $secondFileFullPath = "{$pathToFiles}{$secondFileName}";
    if (!isFilesExists($firstFileFullPath, $secondFileFullPath)) {
        line('Error: one of files does not exists.');
        return;
    }
    if (!isFilesExtensionSame($firstFileName, $secondFileName)) {
        line('Error: files extensions are not the same.');
        return;
    }
    [, $filesExtension] = explode('.', $firstFileName);
    try {
        $parser = new FileParser($firstFileFullPath, $secondFileFullPath, $filesExtension);
        $result = $parser->parseFiles();
        $ast = AST\makeAst(...$result);
        $report = generateReport($format, $ast);
    } catch (\Exception $error) {
        line($error);
        return;
    }
    line($report);
}
