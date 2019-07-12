<?php

namespace Gendiff\Tests;

use function Gendiff\Utils\FileUtils\getJsonFileContent;
use function Gendiff\Utils\FileUtils\getYamlFileContent;
use function Gendiff\Main\generateDiff;
use PHPUnit\Framework\TestCase;

class GenDiffTest extends TestCase
{
    public function testGenerateDiff()
    {
        $testDir = './tests/';
        $firstFile = getJsonFileContent('a.json', $testDir);
        $secondFile = getJsonFileContent('b.json', $testDir);
        $report = generateDiff($firstFile, $secondFile);
        $this->assertEquals("{
    host: hexlet.io
    + timeout: 20
    - timeout: 50
    - proxy: 123.234.53.22
    + verbose: true
}", $report);
        $this->assertEquals("{
    host: hexlet.io
    timeout: 50
    proxy: 123.234.53.22
}", generateDiff($firstFile, $firstFile));
        $this->assertEquals("{
    - host: hexlet.io
    - timeout: 50
    - proxy: 123.234.53.22
}", generateDiff($firstFile, []));

        $this->assertEquals("{
    + host: hexlet.io
    + timeout: 50
    + proxy: 123.234.53.22
}", generateDiff([], $firstFile));

        $firstFile = getYamlFileContent("a.yaml", $testDir);
        $secondFile = getYamlFileContent("b.yaml", $testDir);
        $report = generateDiff($firstFile, $secondFile);
        $this->assertEquals("{
    host: hexlet.io
    + timeout: 20
    - timeout: 50
    - proxy: 123.234.53.22
    + verbose: true
}", $report);
        $this->assertEquals("{
    host: hexlet.io
    timeout: 50
    proxy: 123.234.53.22
}", generateDiff($firstFile, $firstFile));
        $this->assertEquals("{
    - host: hexlet.io
    - timeout: 50
    - proxy: 123.234.53.22
}", generateDiff($firstFile, []));

        $this->assertEquals("{
    + host: hexlet.io
    + timeout: 50
    + proxy: 123.234.53.22
}", generateDiff([], $firstFile));
    }
}
