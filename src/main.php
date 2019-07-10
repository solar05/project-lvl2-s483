<?php

namespace Gendiff\Main;

use Docopt;
use function Funct\Collection\get;
use Gendiff\Utils\FileUtils;

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
    var_dump($args->args);
    [$firstArg, $secondArg] = $args->args['<file>'];
    $pathToFiles = $args->args['--path'];
    $firstFile = FileUtils\getJsonFileContent($firstArg, $pathToFiles);
    $secondFile = FileUtils\getJsonFileContent($secondArg, $pathToFiles);
    var_dump($firstFile);
    var_dump($secondFile);
}
