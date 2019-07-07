<?php

namespace Gendiff\Main;

use Docopt;

function run()
{
    $documentation = "
Generate diff

Usage:
gendiff (-h|--help)
gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  --format <fmt>                Report format [default: pretty]
";
    $args = Docopt::handle($documentation, array('version' => 'GenDiff 1.0'));
}
