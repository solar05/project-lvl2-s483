<?php

namespace Gendiff\Main;

use function cli\line;
use Docopt;
use function Gendiff\Utils\FileUtils\isFilesExtensionSame;
use function Gendiff\Utils\FileUtils\isFilesExists;
use Gendiff\FileParser\FileParser;
use Gendiff\AST;

function run()
{
    $documentation = "
Generate diff

Usage: gendiff <file> <file> --path=<path>
gendiff (-h|--help)
gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  --format <fmt>                Report format [default: pretty]
";
    $args = Docopt::handle($documentation, array('version' => 'GenDiff 1.0'));
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
    } catch (\Exception $error) {
        line($error);
        return;
    }
    var_dump($result);
}
