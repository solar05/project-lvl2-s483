<?php

namespace Gendiff\Main;

use function cli\line;
use Docopt;
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
    [$firstArg, $secondArg] = $args->args['<file>'];
    $pathToFiles = $args->args['--path'];
    $firstFile = FileUtils\getJsonFileContent($firstArg, $pathToFiles);
    $secondFile = FileUtils\getJsonFileContent($secondArg, $pathToFiles);
    $report = generateDiff($firstFile, $secondFile);
    line($report);
}

function generateDiff($firstData, $secondData)
{
    $result = "{\n";
    foreach ($firstData as $key => $value) {
        if (array_key_exists($key, $secondData)) {
            if ($firstData[$key] === $secondData[$key]) {
                $result = "{$result}    {$key}: {$value}\n";
            } else {
                $result = "{$result}    + {$key}: {$secondData[$key]}\n    - {$key}: {$value}\n";
            }
        } else {
            $result = "{$result}    - {$key}: {$value}\n";
        }
    }
    foreach ($secondData as $key => $value) {
        if (!array_key_exists($key, $firstData)) {
            if (is_bool($value)) {
                $value = $value ? "true" : "false";
            }
            $result = "{$result}    + {$key}: {$value}\n";
        }
    }
    $result = "{$result}}";
    return $result;
}
